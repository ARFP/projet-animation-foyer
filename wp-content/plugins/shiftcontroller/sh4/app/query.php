<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface SH4_App_IQuery
{
	public function findUserByEmployee( SH4_Employees_Model $employee );
	public function findEmployeeByUser( HC3_Users_Model $user );
	public function findAllUsersWithEmployee();

	public function findManagersForCalendar( SH4_Calendars_Model $calendar );
	public function findViewersForCalendar( SH4_Calendars_Model $calendar );

	public function findCalendarsManagedByUser( HC3_Users_Model $user );
	public function findCalendarsViewedByUser( HC3_Users_Model $user );

	public function filterEmployeesForCalendar( array $employeeList, SH4_Calendars_Model $calendar );
	public function findEmployeesForCalendar( SH4_Calendars_Model $calendar );
	public function findCalendarsForEmployee( SH4_Employees_Model $employee );

	public function findShiftTypesForCalendar( SH4_Calendars_Model $calendar );

	public function filterShiftsForUser( HC3_Users_Model $user, array $shifts );
	public function findCalendarsForChange( SH4_Shifts_Model $model );
}

class SH4_App_Query implements SH4_App_IQuery
{
	public $self, $settings, $permission, $shiftTypesQuery, $calendarsQuery, $employeesQuery, $cp, $t, $usersQuery, $crudFactory;

	public function __construct(
		HC3_Settings $settings,
		HC3_IPermission $permission,

		SH4_ShiftTypes_Query $shiftTypesQuery,
		SH4_Calendars_Query $calendarsQuery,
		SH4_Employees_Query $employeesQuery,
		SH4_Calendars_Permissions $cp,

		HC3_Time $t,
		HC3_Users_Query $usersQuery,
		HC3_CrudFactory $crudFactory,
		HC3_Hooks $hooks
		)
	{
		$this->settings = $hooks->wrap( $settings );
		$this->permission = $hooks->wrap( $permission );
		$this->crudFactory = $hooks->wrap( $crudFactory );
		$this->t = $t;

		$this->shiftTypesQuery = $hooks->wrap( $shiftTypesQuery );
		$this->employeesQuery = $hooks->wrap( $employeesQuery );
		$this->calendarsQuery = $hooks->wrap( $calendarsQuery );
		$this->usersQuery = $hooks->wrap( $usersQuery );
		$this->cp = $hooks->wrap( $cp );

		$this->self = $hooks->wrap( $this );
	}

	public function findCalendarsForChange( SH4_Shifts_Model $shift )
	{
		$employee = $shift->getEmployee();

	// find current shift type
		$startInDay = $shift->getStartInDay();
		$endInDay = $shift->getEndInDay();
		$shiftKey = $startInDay . '-' . $endInDay;

		$calendar = $shift->getCalendar();
		$calendarId = $calendar->getId();

		$needMultiDay = $shift->isMultiDay();

		$ret = $this->self->findCalendarsForEmployee( $employee );
		unset( $ret[$calendarId] );

		$ids = array_keys( $ret );
		foreach( $ids as $id ){
			if( $calendar->isTimeoff() && (! $ret[$id]->isTimeoff() ) ){
				unset( $ret[$id] );
				continue;
			}
			if( $calendar->isShift() && (! $ret[$id]->isShift() ) ){
				unset( $ret[$id] );
				continue;
			}

			$gotShiftTypes = FALSE;
			$thisShiftTypes = $this->self->findShiftTypesForCalendar( $ret[$id] );
			foreach( $thisShiftTypes as $shiftType ){
				$range = $shiftType->getRange();

			// multiday?
				if( $needMultiDay ){
					if( SH4_ShiftTypes_Model::RANGE_HOURS == $range ){
						continue;
					}

				// if this allowed days cover this duration
					$shiftEnd = $shift->getEnd();
					$this->t->setDateTimeDb( $shift->getStart() );
					$minEnd = $this->t->setDateTimeDb( $shift->getStart() )->modify( '+' . $shiftType->getStart() . ' days' )->formatDateTimeDb();
					if( $minEnd > $shiftEnd ){
						continue;
					}
					$maxEnd = $this->t->setDateTimeDb( $shift->getStart() )->modify( '+' . ($shiftType->getEnd() + 1) . ' days' )->formatDateTimeDb();
					if( $maxEnd < $shiftEnd ){
						continue;
					}

					$gotShiftTypes = TRUE;
					break;
				}
				else {
					if( SH4_ShiftTypes_Model::RANGE_DAYS == $range ){
						continue;
					}

					$thisStart = $shiftType->getStart();
					$thisEnd = $shiftType->getEnd();

				// custom time
					if( (NULL === $thisStart) && (NULL === $thisEnd) ){
						$gotShiftTypes = TRUE;
						break;
					}

					$thisKey = $thisStart . '-' . $thisEnd;
					if( $thisKey == $shiftKey ){
						$gotShiftTypes = TRUE;
						break;
					}
				}
			}

			if( ! $gotShiftTypes ){
				unset( $ret[$id] );
				continue;
			}
		}

		return $ret;
	}

	public function filterShiftsForUser( HC3_Users_Model $user, array $return )
	{
		$currentUserId = $user->getId();

		if( ! $currentUserId ){
			$return = array();
			return $return;
		}

		$calendarsAsManager = $this->self->findCalendarsManagedByUser( $user );
		$calendarsAsViewer = $this->self->findCalendarsViewedByUser( $user );

		$calendarsAsEmployee = array();
		$meEmployee = $this->self->findEmployeeByUser( $user );
		if( $meEmployee ){
			$meEmployeeId = $meEmployee->getId();
			$calendarsAsEmployee = $this->self->findCalendarsForEmployee( $meEmployee );
		}

		$ids = array_keys( $return );

		foreach( $ids as $id ){
			$shift = $return[$id];

			$shiftCalendar = $shift->getCalendar();
			$shiftCalendarId = $shiftCalendar->getId();
			$shiftEmployee = $shift->getEmployee();
			$shiftEmployeeId = $shiftEmployee->getId();

			if( isset($calendarsAsManager[$shiftCalendarId]) ){
				continue;
			}

			if( isset($calendarsAsViewer[$shiftCalendarId]) ){
				continue;
			}

			if( isset($calendarsAsEmployee[$shiftCalendarId]) ){
				if( $shiftEmployeeId == $meEmployeeId ){
					if( $shift->isPublished() ){
						$perm = 'employee_view_own_publish';
					}
					else {
						$perm = 'employee_view_own_draft';
					}
				}
				else {
					if( $shift->isOpen() ){
						if( $shift->isPublished() ){
							$perm = 'employee_view_open_publish';
						}
						else {
							$perm = 'employee_view_open_draft';
						}
					}
					else {
						if( $shift->isPublished() ){
							$perm = 'employee_view_others_publish';
						}
						else {
							$perm = 'employee_view_others_draft';
						}
					}
				}

				if( $this->cp->get($shiftCalendar, $perm) ){
					continue;
				}
			}

			if( $shift->isOpen() ){
				if( $shift->isPublished() ){
					$perm = 'visitor_view_open_publish';
				}
				else {
					$perm = 'visitor_view_open_draft';
				}
			}
			else {
				if( $shift->isPublished() ){
					$perm = 'visitor_view_others_publish';
				}
				else {
					$perm = 'visitor_view_others_draft';
				}
			}

			if( $this->cp->get($shiftCalendar, $perm) ){
				continue;
			}

			unset( $return[$id] );
		}

		return $return;
	}

	public function filterCalendarsManagedByUser( array $ret, HC3_Users_Model $user )
	{
		$isAdmin = $this->permission->isAdmin( $user );
		if( $isAdmin ){
			return $ret;
		}

		$userId = $user->getId();
		foreach( $ret as $calendar ){
			$calendarId = $calendar->getId();
			$managers = $this->self->findManagersForCalendar( $calendar );
			if( ! isset($managers[$userId])){
				unset($ret[$calendarId]);
			}
		}

		return $ret;
	}

	public function findCalendarsManagedByUser( HC3_Users_Model $user )
	{
		$ret = $this->calendarsQuery->findActive();
		$ret = $this->self->filterCalendarsManagedByUser( $ret, $user );
		return $ret;
	}

	public function filterCalendarsViewedByUser( array $ret, HC3_Users_Model $user )
	{
		$isAdmin = $this->permission->isAdmin( $user );
		if( $isAdmin ){
			return $ret;
		}

		$userId = $user->getId();
		foreach( $ret as $calendar ){
			$calendarId = $calendar->getId();
			$viewers = $this->self->findViewersForCalendar( $calendar );
			if( ! isset($viewers[$userId])){
				unset($ret[$calendarId]);
			}
		}

		return $ret;
	}

	public function findCalendarsViewedByUser( HC3_Users_Model $user )
	{
		$ret = $this->calendarsQuery->findActive();
		$ret = $this->self->filterCalendarsViewedByUser( $ret, $user );
		return $ret;
	}

	public function findManagersForCalendar( SH4_Calendars_Model $calendar )
	{
		$return = $this->permission->findAdmins();

		$calendarId = $calendar->getId();
		$settingName = 'calendar_' . $calendarId . '_manager';

		$usersIds = $this->settings->get( $settingName, TRUE );
		if( $usersIds ){
			$moreReturn = $this->usersQuery->findManyById( $usersIds );
			foreach( $moreReturn as $id => $user ){
				if( ! array_key_exists($id, $return) ){
					$return[ $id ] = $user;
				}
			}
		}

		return $return;
	}

	public function findViewersForCalendar( SH4_Calendars_Model $calendar )
	{
		// $return = $this->permission->findAdmins();
		$return = array();

		$calendarId = $calendar->getId();
		$settingName = 'calendar_' . $calendarId . '_viewer';

		$usersIds = $this->settings->get( $settingName, TRUE );
		if( $usersIds ){
			$moreReturn = $this->usersQuery->findManyById( $usersIds );
			foreach( $moreReturn as $id => $user ){
				if( ! array_key_exists($id, $return) ){
					$return[ $id ] = $user;
				}
			}
		}

		return $return;
	}

	public function filterEmployeesForCalendar( array $employeeList, SH4_Calendars_Model $calendar )
	{
		$ret = array();

		$calendarId = $calendar->getId();
		$settingName = 'calendar_' . $calendarId . '_employee';
		$employeeIds = $this->settings->get( $settingName, true );

		if( ! $employeeIds ){
			return $ret;
		}

		$ret = $employeeList;

		$ids = array_keys( $ret );
		foreach( $ids as $id ){
			if( ! in_array($id, $employeeIds) ){
				unset( $ret[$id] );
			}
		}

		return $ret;
	}

	public function findEmployeesForCalendar( SH4_Calendars_Model $calendar )
	{
		$return = array();

		$calendarId = $calendar->getId();
		$settingName = 'calendar_' . $calendarId . '_employee';

		$employeesIds = $this->settings->get( $settingName, TRUE );
		if( $employeesIds ){
			$return = $this->employeesQuery->findManyActiveById( $employeesIds );
		}

		return $return;
	}

	public function findShiftTypesForCalendar( SH4_Calendars_Model $calendar )
	{
		$return = array();

		$calendarId = $calendar->getId();
		$settingName = 'calendar_' . $calendarId . '_shifttype';

		$shiftTypesIds = $this->settings->get( $settingName, TRUE );
		if( $shiftTypesIds ){
			$return = $this->shiftTypesQuery->findManyById( $shiftTypesIds );
		}

		return $return;
	}

	public function filterCalendarsForEmployee( array $ret, SH4_Employees_Model $employee )
	{
		$employeeId = $employee->getId();

		$calendarIds = array_keys($ret);
		foreach( $calendarIds as $calendarId ){
			$settingName = 'calendar_' . $calendarId . '_employee';
			$employeesIds = $this->settings->get( $settingName, TRUE );
			if( ! in_array($employeeId, $employeesIds) ){
				unset( $ret[$calendarId] );
			}
		}

		return $ret;
	}

	public function findCalendarsForEmployee( SH4_Employees_Model $employee )
	{
		$ret = $this->calendarsQuery->findActive();
		$ret = $this->self->filterCalendarsForEmployee( $ret, $employee );
		return $ret;
	}

	public function findAllUsersWithEmployee()
	{
		$return = array();

		$crud = $this->crudFactory->make('employee');

		$args = array();
		$results = $crud->read( $args );
		if( ! $results ){
			return $return;
		}

		$usersIds = array();
		foreach( $results as $r ){
			$userId = array_key_exists('user_id', $r) ? $r['user_id'] : NULL;
			if( ! $userId ){
				continue;
			}
			$usersIds[ $userId ] = $userId;
		}

		if( ! $usersIds ){
			return $return;
		}

		$return = $this->usersQuery->findManyById( $usersIds );
		return $return;
	}

	public function findUserByEmployee( SH4_Employees_Model $employee )
	{
		$return = NULL;

		$employeeId = $employee->getId();
		if( ! $employeeId ){
			return $return;
		}

		$crud = $this->crudFactory->make('employee');
		$args = array();
		$args[] = array('id', '=', $employeeId );
		$results = $crud->read( $args );

		if( ! $results ){
			return $return;
		}

		$results = array_shift( $results );
		$userId = array_key_exists('user_id', $results) ? $results['user_id'] : NULL;

		if( ! $userId ){
			return $return;
		}

		$return = $this->usersQuery->findById( $userId );
		return $return;
	}

	public function findEmployeeByUser( HC3_Users_Model $user )
	{
		$return = NULL;

		$userId = $user->getId();
		if( ! $userId ){
			return $return;
		}

		$crud = $this->crudFactory->make('employee');
		$args = array();
		$args[] = array('user_id', '=', $userId );
		$results = $crud->read( $args );

		if( ! $results ){
			return $return;
		}

		$results = array_shift( $results );
		$employeeId = array_key_exists('id', $results) ? $results['id'] : NULL;

		if( ! $employeeId ){
			return $return;
		}

		$return = $this->employeesQuery->findById( $employeeId );
		return $return;
	}
}