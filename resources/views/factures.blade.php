<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" integrity="sha256-zlmAH+Y2JhZ5QfYMC6ZcoVeYkeo0VEPoUnKeBd83Ldc=" crossorigin="anonymous" />

    <script src="bower/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.js" integrity="sha256-SVfZ7rfF8boo4UH6df28wTQeoPEpoQ+xdInu0K2ulYk=" crossorigin="anonymous"></script>
    <script>
        //TODO : CLEAN
        $( document ).ready(function() {
            // count Checkbox selected
            var nb = 0;

            // ===== SELECT ALL CHECKBOX ===== //
            $( "#checkboxSelectAll" ).click(function() {
                nb = 0;
                $('[data-delete]').each(function(index, val){
                    val.checked = true;
                    badgeCount(val)
                });
            });

            // ===== DESELECT ALL CHECKBOX ===== //
            $( "#checkboxDeselectAll" ).click(function() {
                nb = 0;
                updateHTMLBadge();
                $('[data-delete]').each(function(index, val){
                    val.checked = false;
                });
            });

            $("[data-delete]").click(function (index) {
                badgeCount(index.currentTarget)
            });


            // ===== function count checkbox selected ===== //
            function badgeCount(index){
                if (index.checked) {
                    console.log('badge +1');
                    nb++;
                } else {
                    if (nb > 0) {
                        console.log('badge -1');
                        nb--;
                    }
                }
                updateHTMLBadge();
            }

            // ===== function for update html badge ===== //
            function updateHTMLBadge()
            {
                $("#badge-delete").html(nb);

            }

            // ===== AJAX REQUEST for delete facture ===== //
            $( "#btn-delete" ).click(function() {
                $('[data-delete]').each(function(index, val){
                    if(val.checked){
                        var id = val.dataset.delete;

                        $.ajax(
                            {
                                url: "/~sjeremie_Pod9d3GP/facture/delete/"+id,
                                type: 'GET'
                                ,
                                success: function (){
                                    console.log("invoice with id : "+ id +" ,is well deleted");
                                    val.parentNode.parentElement.remove();
                                    nb--;
                                    updateHTMLBadge();
                                    $.toast({
                                        text :"facture id : "+ id +" supprimée",
                                        position: 'top-right',
                                        bgColor: '#a4ffa1',
                                        textColor: '#000'
                                    })
                                },
                                error: function () {
                                    console.log("erreur");
                                    $.toast({
                                        text :"Impossible de supprimer une facture reglée",
                                        position: 'top-right',
                                        bgColor: '#ff294b',
                                        textColor: '#fff'
                                    })
                                }
                            });

                    }
                });
            });
        });
    </script>



</head>
<body>

<div class="jumbotron pb-1">
    <h1 class="display-4">{{ $title }}</h1>
    <hr class="my-4">
    <div class="container justify-content-center">
        <form action="{{ url('facture') }}" method="POST" name="filter">
            {!! csrf_field() !!}
            <div class="form-row justify-content-center">
                <div class="form-group col-md-4 ">
                    <label for="numero">Numero</label>
                    <input type="text" class="form-control" name="numero" id="numero" placeholder="Numero">
                </div>

                <div class="form-group col-md-4">
                    <label for="status">status</label>
                    <input type="text" class="form-control" id="status" name="status" placeholder="status">
                </div>

                <div class="form-group  d-flex align-self-end">
                    <button type="submit" class="btn btn-primary">filtrer</button>
                </div>
            </div>
        </form>
    </div>

</div>



<div class="container">
    @if(session()->has('message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="suA">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(count($factures)  === 0 )
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="suA">
           Aucune correspondance avec ces critreres de recherches
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif




        <div class="justify-content-end d-flex mb-1">
            @if($backIndex)
                <a id="btn-back" href="{{ url('facture') }}"  class="btn btn-primary mr-1" style="color: white">
                    Back list
                </a>
            @endif

            <button id="btn-delete" type="button" class="btn btn-danger ">
                Supprimer
                <span id="badge-delete" class="badge badge-light">0</span>
            </button>
        </div>

    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Numéro</th>
            <th scope="col">Nom & Prénom</th>
            <th scope="col">Date</th>
            <th scope="col">Total facture</th>
            <th scope="col">statut</th>
            <th scope="col">
                <div class="text-center">
                    <span style="cursor:pointer;" id="checkboxSelectAll">Tous</span>
                    /
                    <span style="cursor:pointer;" id="checkboxDeselectAll">Aucun</span>
                </div>
            </th>
        </tr>
        </thead>
        <tfoot class="thead-dark">
        <tr>
            <th scope="col">Numéro</th>
            <th scope="col">Nom & Prénom</th>
            <th scope="col">Date</th>
            <th scope="col">Total facture</th>
            <th scope="col">statut</th>
            <th scope="col">
                <div class="text-center">
                    <span style="cursor:pointer;" id="checkboxSelectAll">Tous</span>
                    /
                    <span style="cursor:pointer;" id="checkboxDeselectAll">Aucun</span>
                </div>
            </th>
        </tr>
        </tfoot>

        <tbody>


        @foreach ($factures as $facture)
            <tr class="{{ number_format($facture->total, 0, ',', '.') < 100 ? 'bg-warning' : '' }}">
                <td>{{$facture->numero}}</td>
                <td>{{$facture->lname}} {{$facture->fname}}</td>
                <td>{{$facture->date}}</td>
                <td>{{number_format($facture->total, 2, ',', '.')}} €</td>
                <td>{{$facture->status}}</td>
                <td class="text-center"><input type="checkbox" aria-label="Checkbox for following text input" data-delete="{{$facture->id}}"></td>
            </tr>


        @endforeach


        </tbody>
    </table>

    <div class="container d-flex justify-content-center ">

        <nav aria-label="Page navigation example ">
            @include('pagination.default', ['paginator' => $factures, 'link_limit' => 5])
        </nav>




    </div>


</div>
</body>
</html>



