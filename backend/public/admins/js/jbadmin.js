var App = function () {
    var daterangepickerConfig = {
        "format": "Y-MM-DD HH:mm:ss",
        "separator": " - ",
        "applyLabel": "OK",
        "cancelLabel": "Bezár",
        "fromLabel": "-tól",
        "toLabel": "-ig",
        //"customRangeLabel": "",
        "daysOfWeek": [
            "Va",
            "Hé",
            "Ke",
            "Sze",
            "Csü",
            "Pé",
            "Szo"
        ],
        "monthNames": [
            "Január",
            "Február",
            "Március",
            "Április",
            "Május",
            "Június",
            "Július",
            "Augusztus",
            "Szeptember",
            "Október",
            "November",
            "December"
        ],
        "firstDay": 1
    };
    var datepickerConfig = {
        "format": "Y-MM-DD",
        "separator": " - ",
        "applyLabel": "OK",
        "cancelLabel": "Bezár",
        "fromLabel": "-tól",
        "toLabel": "-ig",
        //"customRangeLabel": "",
        "daysOfWeek": [
            "Va",
            "Hé",
            "Ke",
            "Sze",
            "Csü",
            "Pé",
            "Szo"
        ],
        "monthNames": [
            "Január",
            "Február",
            "Március",
            "Április",
            "Május",
            "Június",
            "Július",
            "Augusztus",
            "Szeptember",
            "Október",
            "November",
            "December"
        ],
        "firstDay": 1
    };
    return {
        init: function () {
            moment.locale('hu');
            App.onLoadPopOver();
            App.onLoadDateTimers();
            App.onLoadFancyBox();
            App.customFileInputChange();
            App.deleteModal();
            App.AdminSortAble();
            App.menuIconSelectPicker();
        },
        onSaveHasNewsTitleValidatorClass: function () {
            $(".newsTitleValidator").off().on('click', function (e) {
                e.preventDefault();
                var currentForm = $(this).parents("form");
                bootbox.confirm({
                    message: "Do you really want to save it?",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            currentForm.submit();
                        }
                    }
                });
            });
        },
        onLoadFancyBox: function () {
            $(".fancybox").off().on('click', function (e) {
                $.fancybox.open($(this).data('fancyurl'));
            });
        },
        onLoadPopOver: function () {
            $('[data-toggle="popover"]').popover({
                html: true,
                placement: "bottom"
            });
        },
        onLoadDateTimers: function () {
            jQuery('.datettimepicker').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                showDropdowns: true,
                locale: daterangepickerConfig,
            });
            jQuery('.datetpicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: datepickerConfig,
            });

            jQuery('.datethourStart').datetimepicker({
                format: 'Y-m-d 00:00:00',
                lang: 'hu',
                defaultDate: new Date(),
                startDate: new Date()
            });

            jQuery('.datethourFinish').datetimepicker({
                format: 'Y-m-d 23:59:59',
                lang: 'hu',
                defaultDate: new Date(),
                startDate: new Date()
            });
        },
        customFileInputChange: function () {
            if ($('.custom-file-input').length) {
                $('.custom-file-input').on('change', function (e) {
                    var fileName = document.getElementById($(this).attr('id')).files[0].name;
                    var nextSibling = e.target.nextElementSibling
                    nextSibling.innerText = fileName
                })
            }
        },
        menuIconSelectPicker: function () {
            if ($("#menu_Icon").length) {
                $('#menu_Icon').selectpicker();
            }
        },
        deleteModal: function () {
            $(document).on('click', '[data-delete]', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var row = $(this).parents("tr");
                bootbox.dialog({
                    message: 'Deleted data cannot be recovered!',
                    title: 'Are you sure about this?',
                    buttons: {
                        success: {
                            label: 'Yes, I delete',
                            className: "btn-danger",
                            callback: function () {
                                $.ajax({
                                    url: url,
                                    method: "POST",
                                    dataType: "JSON",
                                    data: {
                                        delete: 1
                                    },
                                    success: function (j_response) {
                                        if (j_response.success) {
                                            row.remove();
                                            toastr["success"]('Successful deletion');
                                        } else {
                                            toastr["error"]('Unsuccessful deletion');
                                        }
                                    }
                                });
                            }
                        },
                        danger: {
                            label: 'Cancel',
                            className: "btn-default",
                            callback: function () {
                                $(this).modal('hide');
                            }
                        }
                    }
                }).draggable({
                    handle: ".modal-header"
                });
            });
        },
        AdminSortAble: function () {
            if ($(".js-sortable, .js-sortableSub").length) {
                $(".js-sortable, .js-sortableSub").sortable({
                    placeholder: "ui-state-highlight",
                    items: "tr:not(.ui-state-disabled)",
                    //tolerance: "pointer",
                    /* initialization stuff here */
                    initialize: function () {

                    },
                    /* once an item is selected */
                    start: function (helper, ui) {

                        //Ne lehessen fĹmenĂźbe mĂĄsik fĹmenĂźt belerakni
                        ui.item.parents('table').eq(0).addClass('running');
                        var sizes = {};
                        $(this).find('td').each(function (i, elem) {
                            sizes[i] = $(elem).css('width');
                        });
                        $('.js-sortable tr td').css('z-index', '10');
                        var mI = $('.js-sortable tr[style*="absolute"] td');
                        mI.css('z-index', '100000');
                        mI.each(function (i, elem) {
                            $(elem).css('width', sizes[i]);
                        });
                    },
                    /* when a drag is complete */
                    stop: function (el, ui) {

                        $('.js-sortable tr td').attr('style', '');
                        if (!ui.item.parents('table').eq(0).hasClass('running')) {
                            $('table').removeClass('running');
                            $(".js-sortable").sortable('cancel');
                            return;
                        }

                        $('table').removeClass('running');
                        var urlParams = new URLSearchParams(window.location.search);
                        var actPage = urlParams.get('actpage') != null ? parseInt(urlParams.get('actpage')) : 1;
                        var pageSize = urlParams.get('pagesize') != null ? parseInt(urlParams.get('pagesize')) : 0;
                        if (pageSize === 0 && $(".custom-select option:selected").length) {
                            pageSize = parseInt($(".custom-select option:selected").text())
                        }
                        var counter = (actPage - 1) * pageSize;

                        if ($('.js-sortable').find("tr[data-previndex=0]").length) {
                            var obj = [];
                            $('.js-sortable tr').each(function (i, elem) {
                                var dID = $(elem).attr('data-id');
                                //var newSorrend = $('.js-sortable').find("tr").eq(i).data('sorrend');
                                var newSorrend = $('.js-sortable').find("tr[data-previndex=" + i + "]").eq(0).attr('data-sorrend');
                                if (dID) {
                                    obj.push({ dataid: dID, sorrend: newSorrend });
                                }
                            });
                        } else {
                            var obj = {};
                            $('.js-sortable tr').each(function (i, elem) {
                                var dID = $(elem).attr('data-id');
                                if (dID) {
                                    obj[i + counter] = dID;
                                }
                            });
                        }

                        var query = "";
                        if ($(".js-sortable").data('parent') && $(".js-sortable").data('parent').length) {
                            query = "&" + $(".js-sortable").data('parent');
                        }

                        $.ajax({
                            'url': '/admin/index.php?C=' + $(".js-sortable").attr('data-controller') + '&A=eSort' + query,
                            'data': { "data": obj },
                            'method': 'post'
                        }).done(function () {
                            if ($('.js-sortable').find("tr[data-previndex=0]").length) {
                                var i = 0;
                                for (const mykey in obj) {
                                    $('.js-sortable').find("tr[data-id=" + obj[mykey].dataid + "]").eq(0).attr('data-sorrend', obj[mykey].sorrend)
                                    $('.js-sortable').find("tr[data-id=" + obj[mykey].dataid + "]").eq(0).attr('data-previndex', i);
                                    i++;
                                }
                            }
                        });
                    },
                });
            }
        },
        extractLast: function (term) {
            return App.split(term).pop();
        },
        split: function (val) {
            return val.split(/,\s*/);
        },
        BaseWidgetOnClickCopyToClipboard: function () {
            $(".copytoclipboard").off().on('click', function (e) {
                e.preventDefault();
                App.BaseWidgetCopyToClipboard($(this).data('id'));
                toastr["success"]("Successful clipboard insertion!");
            });
        },
    }
}();
(function ($) {
    'use strict';
    $(document).ready(function () {
        App.init();
    });
})(jQuery);


