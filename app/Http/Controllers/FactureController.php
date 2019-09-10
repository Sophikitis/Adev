<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class FactureController extends Controller
{
    public function index(){

        //Retrieve lines
        $factures = DB::table('facture AS f' )
            ->selectRaw('f.id, f.numero, f.lname, f.fname, f.date, f.status, sum(lf.unit_price) AS total')
            ->join('ligne_facture AS lf', 'f.id', '=', 'lf.ide_facture')
            ->groupBy('f.id')
            ->paginate(15);

/*        $factures->withPath('/~sjeremie_Pod9d3GP/');*/

        //Return data to the view
        return view('factures', [
            'title' => 'Liste des Factures',
            'factures' => $factures,
            'backIndex' => false

        ]);
    }





    public function search(){
        //Retrieve input "filter"
        $filter = Input::except('lnamefname');
        $inputToWhere = [];
        $pdo = DB::connection()->getPdo();

        //Recupere les noms des colonnes de la table factures.
        $nameColumnFactureBdd = $pdo->query(
           '
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA. COLUMNS
            WHERE TABLE_NAME = \'facture\'
            ORDER BY ORDINAL_POSITION
           '
             )->fetchAll(\PDO::FETCH_COLUMN);


        // Si le nom de l'input n'a pas de corespondance dans le tableau des noms de colonnes recuperer precdedement
        // on ne l'ajoute pas dans le le tableau des conditions pour la requete.
        // On verifie egalement que la saisie de contient pas uniquement des espaces, si c'est le cas, on n'ajoute pas
        // au tableau des conditions
        foreach ($filter as $item => $value){
            if($item !== '_token' && !empty(trim($value)) && in_array($item, $nameColumnFactureBdd, true)) {
                $inputToWhere[$item] = $value;
            }
        }

        // Control if inputToWhere is empty : if empty = return index function
        if(empty($inputToWhere)){
            return redirect()->back()->with('message', 'Saisie non valide!');
        }


        $sqlSearch = DB::table('facture AS f' )
            ->selectRaw('f.id, f.numero, f.lname, f.fname, f.date, f.status, sum(lf.unit_price) AS total')
            ->join('ligne_facture AS lf', 'f.id', '=', 'lf.ide_facture')
            ->where($inputToWhere)
            ->groupBy('f.id')
            ->paginate(10);


        return view('factures', [
            'title' => 'Liste des Factures',
            'factures' => $sqlSearch,
            'backIndex' => true
        ]);
    }





    public function delete($id){

        $facture = DB::table('facture')->where('id', '=', $id)->get();

        if($facture[0]->status !== "FACTURE_REGLE"){
            DB::table('facture')->where('id', '=', $id)->delete();
            return response()->json([
                'success' => 'Enregistrement supprimé'
            ]);
        }

        return response()->json([
            'error' => 'erreur'
        ],404);

    }

    /* return simple view for welcome page */
    public function accueil() {
        return view('accueil', []);
    }
}
