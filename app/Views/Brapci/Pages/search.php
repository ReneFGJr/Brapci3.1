<?php
$submit = '<input type="submit" name="action" class="btn btn-primary shadow p-3 mb-0 text-lg" type="button" value="' . lang('main.search') . '">';
$input_field = '<input type="text" name="query" value="" class="form-control shadow" placeholder="O que você está procurando?">';
$types = array('all', 'article', 'proceeding', 'bnancib', 'authorirty');
$select_type = '<select id="type" name="collection" class="form-control shadow" style="border: 1px solid #ccc; font-size: 130%; line-hight: 150%; max-width: 250px;">';
for ($r = 0; $r < count($types); $r++) {
    $select_type .= '<option value="all">' . lang('main.' . $types[$r]) . '</option>' . cr();
}
$select_type .= '</select>';

/*********************************** ORDER */
$order = array('relevance', 'newest', 'older');
$sord = '';
$ord = get("ord");
if ($ord == '') {
    $ord = '1';
}
for ($r = 0; $r < count($order); $r++) {
    $check = '';
    if ($ord == $r) {
        $check = 'checked';
    }
    $sord .= '<option value="' . $r . '" ' . $check . '">' . lang('brapci.' . $order[$r]) . '</option>' . cr();
}

/*********************************** ORDER */
$fields = array('all', 'title', 'abstract', 'keyword');
$sfield = '';
$field = get("field");
if ($ord == '') {
    $ord = '1';
}
for ($r = 0; $r < count($fields); $r++) {
    $check = '';
    if ($ord == $r) {
        $check = 'checked';
    }
    $sfield .= '<option value="' . $r . '" ' . $check . '">' . lang('brapci.' . $fields[$r]) . '</option>' . cr();
}

/*********************************** SDI */
$ini = 1960;
$di = get("di");
if ($di == '') {
    $di = $ini;
}
$sdi = '';
for ($r = $ini; $r <= (date("Y") + 1); $r++) {
    $check = '';
    if ($r == $di) {
        $check = 'checked';
    }
    $sdi .= '<option value="' . $r . '" ' . $check . '>' . $r . '</option>' . cr();
}
/*********************************** SDF */
$df = get("df");
if ($df == '') {
    $df = date("Y");
}
$sdf = '';
for ($r = (date("Y") + 1); $r >= $ini; $r--) {
    $check = '';
    if ($r == $df) {
        $check = 'checked';
    }
    $sdf .= '<option value="' . $r . '" ' . $check . '>' . $r . '</option>' . cr();
}

?>
<div class="container" style="margin-top: 100px;">
    <div class="row ">
        <div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
            <div style="height: 150px;" class="text-center">
                <style>
                    #logoB {
                        fill: green;
                        animation: col 1s alternate infinite;
                    }

                    #logoR {
                        animation: col 1s alternate infinite;
                    }

                    #logoA {
                        animation: col 1s alternate infinite;
                    }

                    #logoP {
                        animation: col 1s alternate infinite;
                    }

                    #logoC {
                        fill: brown;
                        animation: col 1s alternate infinite;
                    }

                    #logoI {
                        fill: green;
                        animation: col 1s alternate infinite;
                    }

                    @keyframes col {
                        from {
                            fill: #0d6efd;
                        }

                        to {
                            fill: #2daefd;
                        }
                    }

                    }
                </style>
                <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="75%" viewBox="0 0 649.000000 150.000000" preserveAspectRatio="xMidYMid meet">
                    <metadata>
                        Logo Brapci SVG (2022)
                    </metadata>
                    <g transform="translate(0.000000,150.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none" style="width: 100%;">
                        <path id="logoB" d="M260 745 l0 -555 293 0 c502 1 590 22 660 155 26 50 35 176 17 240
           -15 53 -64 113 -114 140 -34 18 -47 35 -27 35 18 0 103 85 123 124 49 95 30
           240 -40 318 -81 89 -132 98 -569 98 l-343 0 0 -555z m659 352 c49 -17 73 -55
           74 -116 1 -60 -17 -97 -58 -119 -24 -14 -68 -17 -237 -20 l-208 -4 0 136 0
           136 198 -1 c125 0 209 -4 231 -12z m27 -466 c57 -35 70 -141 26 -200 -30 -41
           -79 -49 -289 -50 l-193 -1 0 135 0 135 213 0 c191 0 215 -2 243 -19z" />
                        <path id="logoR" d="M1440 746 l0 -556 110 0 110 0 0 220 0 221 147 -3 146 -3 121 -217
           121 -218 128 0 129 0 -20 33 c-11 17 -74 122 -140 232 l-120 199 68 33 c111
           54 154 133 154 288 0 122 -23 185 -90 244 -82 72 -103 75 -506 79 l-358 3 0
           -555z m668 341 c61 -33 80 -137 37 -203 -32 -49 -72 -58 -287 -62 l-198 -4 0
           147 0 146 208 -3 c177 -3 212 -6 240 -21z" />
                        <path id="logoA" d="M2726 764 c-125 -295 -234 -544 -243 -553 -15 -15 -8 -16 102 -19 65
           -1 124 -1 130 2 6 2 33 63 60 135 l48 131 247 0 247 0 50 -132 50 -133 127 -3
           127 -3 -16 38 c-9 21 -115 271 -237 556 l-221 517 -122 0 -122 0 -227 -536z
           m427 115 c42 -108 79 -204 83 -213 6 -14 -11 -16 -165 -16 -94 0 -171 2 -171
           5 0 20 165 434 170 428 4 -4 41 -96 83 -204z" />
                        <path id="logoP" d="M3760 746 l0 -556 110 0 110 0 2 197 3 198 250 6 c265 6 291 10 367
           58 100 64 144 165 136 315 -6 114 -37 185 -108 249 -86 77 -104 80 -512 85
           l-358 4 0 -556z m628 349 c73 -22 97 -58 97 -150 0 -87 -23 -125 -93 -152 -39
           -15 -77 -18 -227 -18 l-180 0 -3 168 -2 167 178 0 c131 0 192 -4 230 -15z" />
                        <path id="logoC" d="M5201 1284 c-178 -38 -282 -142 -332 -331 -28 -104 -30 -317 -5 -413
           46 -171 162 -292 318 -330 24 -6 161 -14 306 -17 l262 -6 0 96 0 95 -242 4
           c-263 4 -286 8 -340 63 -58 58 -85 177 -74 337 11 168 54 265 139 311 40 21
           56 22 280 25 l237 4 0 89 0 89 -242 -1 c-168 0 -263 -5 -307 -15z" />
                        <path id="logoI" d="M5930 745 l0 -555 120 0 120 0 0 555 0 555 -120 0 -120 0 0 -555z" />
                    </g>
                </svg>
            </div>
        </div>
    </div>
</div>
<div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
    <h3 class="" style="">O que você está procurando?</h3>
</div>
<div class="container" style="margin-bottom: 20px" ;>
    <!-------------------------- SMALL SCREEN ----------->
    <div class="row d-lg-none">
        <div class="col-12">
            <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                <?= $input_field; ?>
            </div>
        </div>

        <div class="col-12">
            <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                <?= $select_type; ?>
            </div>
            <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                <?= $submit; ?>
            </div>
        </div>
    </div>
    <!-------------------------- BIG SCREEN ----------->
    <div class="row d-none d-lg-block">
        <div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
            <form method="get">
                <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                    <?= $input_field; ?>
                    <?= $select_type; ?>
                    <?= $submit; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container" style="margin-bottom: 250px" ;>
    <!-------------------------- ADVANCED SCREEN ----------->
    <div class="row ">
        <div id="search_type" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <span class="a" style="cursor: pointer" onclick="$('#search_type').toggle(); $('#advanced_search').toggle('slow');">Busca avançada</span>
        </div>
    </div>

    <div class="row border border-secondary p-2 mb-2 rounded-3 m-3 shadow" id="advanced_search" style="display: none;">
        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
            <?= lang('brapci.delimitation'); ?>&nbsp;
            <select name="di" class="border-0 fw-bold"><?= $sdi; ?></select>&nbsp;
            <select name="df" class="border-0 fw-bold"><?= $sdf; ?></select>&nbsp;
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
            <?= lang('brapci.ordenation'); ?>&nbsp;
            <select name="ord" class="border-0 fw-bold"><?= $sord; ?></select>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
            <?= lang('brapci.fields'); ?>&nbsp;
            <select name="ord" class="border-0 fw-bold"><?= $sfield; ?></select>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-2">
            <select name="publication" id="publication" class="form-control">
                <option value="1">American Black Bear</option>
                <option value="2">Asiatic Black Bear</option>
                <option value="3">Brown Bear</option>
                <option value="4">Giant Panda</option>
                <option value="5">Sloth Bear</option>
                <option value="6">Sun Bear</option>
                <option value="7">Polar Bear</option>
                <option value="8">Spectacled Bear</option>
            </select>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#publication").CreateMultiCheckBox({
            width: '600px',
            defaultText: 'Select Below',
            height: '250px'
        });
    });

    $(document).ready(function() {
        $(document).on("click", ".MultiCheckBox", function() {
            var detail = $(this).next();
            detail.show();
        });

        $(document).on("click", ".MultiCheckBoxDetailHeader input", function(e) {
            e.stopPropagation();
            var hc = $(this).prop("checked");
            $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", hc);
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
        });

        $(document).on("click", ".MultiCheckBoxDetailHeader", function(e) {
            var inp = $(this).find("input");
            var chk = inp.prop("checked");
            inp.prop("checked", !chk);
            $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", !chk);
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
        });

        $(document).on("click", ".MultiCheckBoxDetail .cont input", function(e) {
            e.stopPropagation();
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();

            var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
            $(".MultiCheckBoxDetailHeader input").prop("checked", val);
        });

        $(document).on("click", ".MultiCheckBoxDetail .cont", function(e) {
            var inp = $(this).find("input");
            var chk = inp.prop("checked");
            inp.prop("checked", !chk);

            var multiCheckBoxDetail = $(this).closest(".MultiCheckBoxDetail");
            var multiCheckBoxDetailBody = $(this).closest(".MultiCheckBoxDetailBody");
            multiCheckBoxDetail.next().UpdateSelect();

            var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
            $(".MultiCheckBoxDetailHeader input").prop("checked", val);
        });

        $(document).mouseup(function(e) {
            var container = $(".MultiCheckBoxDetail");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });
    });

    var defaultMultiCheckBoxOption = {
        width: '220px',
        defaultText: 'Select Below',
        height: '200px'
    };

    jQuery.fn.extend({
        CreateMultiCheckBox: function(options) {

            var localOption = {};
            localOption.width = (options != null && options.width != null && options.width != undefined) ? options.width : defaultMultiCheckBoxOption.width;
            localOption.defaultText = (options != null && options.defaultText != null && options.defaultText != undefined) ? options.defaultText : defaultMultiCheckBoxOption.defaultText;
            localOption.height = (options != null && options.height != null && options.height != undefined) ? options.height : defaultMultiCheckBoxOption.height;

            this.hide();
            this.attr("multiple", "multiple");
            var divSel = $("<div class='MultiCheckBox'>" + localOption.defaultText + "<span class='k-icon k-i-arrow-60-down'><svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='sort-down' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512' class='svg-inline--fa fa-sort-down fa-w-10 fa-2x'><path fill='currentColor' d='M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z' class=''></path></svg></span></div>").insertBefore(this);
            divSel.css({
                "width": localOption.width
            });

            var detail = $("<div class='MultiCheckBoxDetail'><div class='MultiCheckBoxDetailHeader'><input type='checkbox' class='mulinput' value='-1982' /><div>Select All</div></div><div class='MultiCheckBoxDetailBody'></div></div>").insertAfter(divSel);
            detail.css({
                "width": parseInt(options.width) + 10,
                "max-height": localOption.height
            });
            var multiCheckBoxDetailBody = detail.find(".MultiCheckBoxDetailBody");

            this.find("option").each(function() {
                var val = $(this).attr("value");

                if (val == undefined)
                    val = '';

                multiCheckBoxDetailBody.append("<div class='cont'><div><input type='checkbox' class='mulinput' value='" + val + "' /></div><div>" + $(this).text() + "</div></div>");
            });

            multiCheckBoxDetailBody.css("max-height", (parseInt($(".MultiCheckBoxDetail").css("max-height")) - 28) + "px");
        },
        UpdateSelect: function() {
            var arr = [];

            this.prev().find(".mulinput:checked").each(function() {
                arr.push($(this).val());
            });

            this.val(arr);
        },
    });
</script>

<style>
    .MultiCheckBox {
        border: 1px solid #e2e2e2;
        padding: 5px;
        border-radius: 4px;
        cursor: pointer;
    }

    .MultiCheckBox .k-icon {
        font-size: 15px;
        float: right;
        font-weight: bolder;
        margin-top: -7px;
        height: 10px;
        width: 14px;
        color: #787878;
    }

    .MultiCheckBoxDetail {
        display: none;
        position: absolute;
        border: 1px solid #e2e2e2;
        overflow-y: hidden;
    }

    .MultiCheckBoxDetailBody {
        overflow-y: scroll;
    }

    .MultiCheckBoxDetail .cont {
        clear: both;
        overflow: hidden;
        padding: 2px;
    }

    .MultiCheckBoxDetail .cont:hover {
        background-color: #cfcfcf;
    }

    .MultiCheckBoxDetailBody>div>div {
        float: left;
    }

    .MultiCheckBoxDetail>div>div:nth-child(1) {}

    .MultiCheckBoxDetailHeader {
        overflow: hidden;
        position: relative;
        height: 28px;
        background-color: #3d3d3d;
    }

    .MultiCheckBoxDetailHeader>input {
        position: absolute;
        top: 4px;
        left: 3px;
    }

    .MultiCheckBoxDetailHeader>div {
        position: absolute;
        top: 5px;
        left: 24px;
        color: #fff;
    }
</style>