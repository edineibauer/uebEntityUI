<?php
ob_start();
?>

    <div class="container padding-32">
        <div class="container-900">
            <h2 class="font-light padding-small padding-16">Meus Sites</h2>

            <div class="col" id="sites">
                <div class="col padding-small s12 m3 l4">
                    <div class="card padding-medium align-center padding-32 pointer hover-shadow transition-easy">
                        <i class="material-icons font-jumbo">add</i>
                    </div>
                </div>
            </div>

        </div>
    </div>


<?php
$data['data'] = ob_get_contents();
ob_end_clean();