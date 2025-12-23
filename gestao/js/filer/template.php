<?php if(isset($funcao_capa) && !$funcao_capa){ ?>
    <style type="text/css">
        .jFiler-item-others .icon-jfi-file-o{
            display: none;
        }
    </style>
<?php } ?>

<?php if(isset($width_item) && $width_item){ ?>
    <style type="text/css">
        .jFiler-items-grid .jFiler-item{
            width: <?php echo $width_item ?>%!important;
        }
    </style>
<?php } ?>
<input type="file" name="files[]" id="<?php echo $filer_id_input ?>" multiple="multiple">
<script type="text/javascript">
    $(document).ready(function(){

        //Example 2
        $("#<?php echo $filer_id_input ?>").filer({
            limit: null,
            maxSize: null,
            extensions: null,
            changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Arraste&Solte suas imagens aqui</h3> <span style="display:inline-block; margin: 15px 0">ou</span></div><a class="jFiler-input-choose-btn blue">Procurar imagens</a></div></div>',
            showThumbs: true,
            theme: "dragdropbox",
            templates: {
                box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
                item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-thumb-overlay">\
                                        <div class="jFiler-item-info">\
                                            <div style="display:table-cell;vertical-align: middle;">\
                                                <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
                itemAppend: '<li class="jFiler-item">\
                                <div class="jFiler-item-container">\
                                    <div class="jFiler-item-inner">\
                                        <div class="jFiler-item-thumb">\
                                            <div class="jFiler-item-status"></div>\
                                            <div class="jFiler-item-thumb-overlay">\
                                                <div class="jFiler-item-info">\
                                                    <div style="display:table-cell;vertical-align: middle;">\
                                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
                                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            {{fi-image}}\
                                        </div>\
                                        <div class="jFiler-item-assets jFiler-row">\
                                            <ul class="list-inline pull-left">\
                                                <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                            </ul>\
                                            <ul class="list-inline pull-right">\
                                                <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                            </ul>\
                                        </div>\
                                    </div>\
                                </div>\
                                </li>',
                progressBar: '<div class="bar"></div>',
                itemAppendToEnd: false,
                canvasImage: true,
                removeConfirmation: true,
                _selectors: {
                    list: '.jFiler-items-list',
                    item: '.jFiler-item',
                    progressBar: '.bar',
                    remove: '.jFiler-item-trash-action'
                }
            },
            dragDrop: {
                dragEnter: null,
                dragLeave: null,
                drop: null,
                dragContainer: null,
            },
            uploadFile: {
                url: "./js/filer/uploader.php",
                data: {
                    path: '<?php echo $filer_path_upload; ?>',
                    id_registro: '<?php echo $id_table; ?>',
                    classe: '<?php echo $filer_classe ?>'
                },
                type: 'POST',
                enctype: 'multipart/form-data',
                synchron: true,
                beforeSend: function(){},
                success: function(data, itemEl, listEl, boxEl, newInputEl, inputEl, id){
                    var parent = itemEl.find(".jFiler-jProgressBar").parent(),
                        new_file_name = JSON.parse(data),
                        filerKit = inputEl.prop("jFiler");
                    itemEl.children('div').attr('data-id-image',data)
                    $('.jFiler-items-list').find('.jFiler-item-container').addClass('no-capa');
                    $('.jFiler-items-list').find('.capa').removeClass('no-capa');
                    itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                        $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Sucesso</div>").hide().appendTo(parent).fadeIn("slow");
                    });
                },
                error: function(el){
                    var parent = el.find(".jFiler-jProgressBar").parent();
                    el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                        $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Erro</div>").hide().appendTo(parent).fadeIn("slow");
                    });
                },
                statusCode: null,
                onProgress: null,
                onComplete: null
            },
            files: [
                <?php if($imagens){
                    foreach($imagens as $imagem){
                        $item = '{';
                        $item .= 'name: "'.$imagem->url_imagem.'",';
                        $item .= 'capa: '.(isset($funcao_capa) && $funcao_capa ? ($imagem->capa == 'S' ? 'true' : 'false') : 'false').',';
                        $item .= 'id_image: '.$imagem->{$id_imagem_key}.',';
                        $item .= 'type: "image/png",';
                        $item .= 'file: "'.BASE_SITE_URL.'/'.$filer_path_upload.$imagem->url_imagem.'"';
                        $item .= '},
                        ';
                        echo $item;
                    }
                } ?>
            ],
            addMore: false,
            allowDuplicates: true,
            clipBoardPaste: true,
            excludeName: null,
            beforeRender: null,
            afterRender: null,
            beforeShow: null,
            beforeSelect: null,
            onSelect: null,
            afterShow: null,
            onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
                var filerKit = inputEl.prop("jFiler"),
                    file_name = filerKit.files_list[id].name;
                var id_image = $(filerKit.files_list[id].html).find('.jFiler-item-container').attr('data-id-image');

                $.ajax({
                    method: "POST",
                    url: "./js/filer/remover.php",
                    dataType: 'html',
                    data: {
                        removeImage: 1,
                        classe: '<?php echo $filer_classe ?>',
                        id_image: id_image
                    },
                    success: function(retorno){
                        if(retorno == 'OK'){
                            //window.location.reload();
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            },
            onEmpty: null,
            options: null,
            dialogs: {
                alert: function(text) {
                    return alert(text);
                },
                confirm: function (text, callback) {
                    confirm(text) ? callback() : null;
                }
            },
            captions: {
                button: "Procurar imagens",
                feedback: "Procurar imagens para upload",
                feedback2: "Imagens encontradas",
                drop: "Solte imagens aqui para fazer upload",
                removeConfirmation: "Tem certeza que deseja remover esta imagem?",
                errors: {
                    filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                    filesType: "Only Images are allowed to be uploaded.",
                    filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                    filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
                }
            }
        });
    });
    $(document).on('click', '.no-capa .icon-jfi-file-o', function(){
        var $this = this;
        $.ajax({
            method: "POST",
            url: "./js/filer/capa.php",
            dataType: 'html',
            data: {
                setCapa: 1,
                classe: '<?php echo $filer_classe ?>',
                id_image: $($this).closest('.no-capa').attr('data-id-image')
            },
            success: function(retorno){
                if(retorno == 'OK'){
                    $('.jFiler-items-list').find('.jFiler-item-container').removeClass('no-capa').removeClass('capa');
                    $('.jFiler-items-list').find('.jFiler-item-container').addClass('no-capa');
                    $($this).closest('.jFiler-item-container').removeClass('no-capa').addClass('capa');
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    });
</script>