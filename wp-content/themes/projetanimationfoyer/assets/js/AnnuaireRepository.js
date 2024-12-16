class AnnuaireRepository
{
    static apiBaseUrl = 'http://localhost:3000/api';

    async fetchBenevolesData()
    {
        try
        {
            const response = await fetch(AnnuaireRepository.apiBaseUrl + '/benevoles');
            const data = await response.json();
            return data;
        }
        catch (error)
        {
            console.error('Echec de récupération des données via API');
            throw error;
        }
    }
}

export { AnnuaireRepository }
