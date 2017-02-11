<!DOCTYPE html>
<html lang="en" xmlns:v-on="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Evirad 2</title>

        <link rel="icon" href="favicon.ico"/>

        <link rel="stylesheet" href="css/app.css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script>
            window.Laravel = {!! json_encode(['csrfToken' => csrf_token()])!!};
        </script>
    </head>
    <body>
        <div class="wrap">

            <nav class="navbar navbar-inverse navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                                aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Evirad 2</a>
                    </div>
                    <div id="navbar" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#">Home</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>

            <div class="container-fluid text-center" id="app">
                <div class="row">
                    <div class="col-sm-2 sidebar">
                    </div>

                    <div class="col-sm-8 text-left content">
                        <h1>RADNICI</h1>

                        <div class="row">
                            <div class="col-sm-3 lista">
                                <div v-show="listLoading" class="loader-overlay"><div class="loader-width-limiter"><div class="loader-container"><div class="loader"></div></div></div></div>
                                <input type="text" class="form-control" v-model="searchQuery"/>
                                <div class="list-group">
                                    <a v-for="radnik in filteredRadnici" v-cloak data-toggle="popover"
                                       :class="'list-group-item' + ((radnik.id==radnikDetails.id)?' active':'')"
                                       :data-original-title="radnik.ime + ' ' + radnik.prezime"
                                       :data-content="'Komentar: '+radnik.komentar + '</br></br>Kreiran: ' + radnik.created_at"
                                       href="#" @click.prevent="loadDetailsClick(radnik.id)"> @{{ radnik.prezime }} @{{ radnik.ime }}
                                    </a>
                                </div>
                            </div>

                            <div class="col-sm-9 forma">
                                <div class="form-group">
                                    <button class="btn btn-default" @click.prevent="noviRadnikClick()">Novi radnik</button>
                                </div>
                                <div v-cloak :class="'alert alert-dismissable alert-' + message.type" v-if="message.type">
                                    <a href="#" class="close" @click.prevent="message={}" aria-label="close">&times;</a>
                                    @{{ message.text }}
                                </div>
                                <div v-cloak v-show="showDetails">
                                    <div v-show="detailsLoading" class="loader-overlay"><div class="loader-width-limiter"><div class="loader-container"><div class="loader"></div></div></div></div>
                                    <h1>@{{ radnikDetailsTitle }}</h1>
                                    <form @submit.prevent="sendData()" enctype="application/x-www-form-urlencoded">
                                        <div :class="'form-group' + (errors.ime?' has-error':'')">
                                            <label class="control-label" for="ime">Ime:</label>
                                            <input type="text" class="form-control" id="ime" name="ime" v-model="radnikDetails.ime">
                                            <span class="help-block" v-if="errors.ime">@{{ errors.ime[0] }}</span>
                                        </div>
                                        <div :class="'form-group' + (errors.prezime?' has-error':'')">
                                            <label class="control-label" for="ime">Prezime:</label>
                                            <input type="text" class="form-control" id="prezime" name="prezime" v-model="radnikDetails.prezime">
                                            <span class="help-block" v-if="errors.prezime">@{{ errors.prezime[0] }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="komentar">Komentar:</label>
                                            <input type="text" class="form-control" id="komentar" name="komentar" v-model="radnikDetails.komentar">
                                        </div>
                                        <div :class="'form-group' + (errors.sluzba?' has-error':'')">
                                            <label class="control-label" for="sluzba">Sluzba:</label>
                                            <loading-select name="sluzba" v-model="selectedSluzba" :options="sluzbe" :loading="sluzbeLoading" class="form-control" id="sluzba" title="-- Odaberi sluzbu --"></loading-select>
                                            <span class="help-block" v-if="errors.sluzba">@{{ errors.sluzba[0] }}</span>
                                        </div>
                                        <sluzba-dialog v-if="showSluzbaDialog" @close="showSluzbaDialog = false" @ok="sluzbaCreated(arguments[0])">
                                        </sluzba-dialog>

                                        <div :class="'form-group' + (errors.grupa?' has-error':'')">
                                            <label class="control-label" for="grupa">Grupa:</label>
                                            <loading-select name="grupa" v-model="selectedGrupa" :options="grupe2" :loading="grupeLoading" class="form-control" id="grupa" title="-- Odaberi grupu --"></loading-select>
                                            <span class="help-block" v-if="errors.grupa">@{{ errors.grupa[0] }}</span>
                                        </div>
                                        <grupa-dialog v-if="showGrupaDialog" @close="showGrupaDialog = false" @ok="grupaCreated(arguments[0])" :grupe="grupe">
                                        </grupa-dialog>

                                        <h3>Kartice:</h3>
                                        <div id="kartice">
                                            <div class="form-group" v-for="(kartica,index) in radnikDetails.kartice" v-cloak>
                                                <label class="control-label" :for="'kartice_'+index+'_kod'">Kod @{{ index+1 }}:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" :id="'kartice_'+index+'_kod'" :name="'kartice['+index+'][kod]'" v-model="kartica.kod">
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn btn-default" @click.prevent="radnikDetails.kartice.splice(index,1)"><span class="glyphicon glyphicon-trash"></span></button>
                                                    </div>
                                                </div>

                                                <input type="hidden" :name="'kartice['+index+'][id]'" :value="kartica.id">
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label" for="kartica_kod">Kod:</label>
                                                <input type="text" class="form-control" id="kartica_kod" v-model="karticaKod" @blur="karticaKodBlur">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-default">Submit</button>
                                        <button type="button" class="btn btn-default" v-if="radnikDetails.id" @click.prevent="deleteRadnik"><span class="glyphicon glyphicon-trash"></span></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 sidebar">
                    </div>
                </div>
            </div>

        </div>

        <footer class="footer">
            <div class="container-fluid footer-content">
                <div id="version">Evirad v2.0.0</div>
                <p class="pull-left">&copy; HEX {{ date('Y') }}</p>
                <p class="pull-right">Powered by <a href="https://laravel.com/" rel="external">Laravel</a></p>
            </div>
        </footer>


        <script src="js/app.js"></script>
    </body>
</html>
