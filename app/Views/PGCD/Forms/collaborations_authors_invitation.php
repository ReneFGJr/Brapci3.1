<table width="100%" style="border: 1px #000 solid">
    <tr>
        <td colspan="2" class="p-2 h5">Enviar Convite</td>
    </tr>
    <tr>
        <td width="60%" class="p-4">
            E-mail <span class="text-danger" id="email_validate" style="color: red;">*</span><br>
            <input type="text" id="ajax_email" class="form-control">
            Nome completo<br>
            <input type="text" id="ajax_nome" class="form-control">
        </td>

        <td width="40%" class="p-4">
            Perfil <span class="text-danger" id="perfil_validate" style="color: red;">*</span><br><br>
            <input type="radio" id="ajax_perfil" name="ajax_prefil" value="1"> <?=lang("pgcd.perfil_1");?><br>
            <input type="radio" id="ajax_perfil" name="ajax_prefil" value="2"> <?=lang("pgcd.perfil_2");?><br>
            <input type="radio" id="ajax_perfil" name="ajax_prefil" value="4"> <?=lang("pgcd.perfil_4");?><br>            
        </td>
    </tr>
</table>
<input type="button" onclick="invite();" value="<?= lang('pgcd.send_invitation'); ?>" class="btn btn-primary mt-3">


<script>
    function invite() {
        $email = $("#ajax_email").val();
        $name = $("#ajax_nome").val();
        $type = $("#ajax_perfil:checked").val();

        /****************************** Email */
        if ($email == "") {
            $("#email_validate").html("* O e-mail obrigatório!");
        } else {
            $("#email_validate").html("*");
        }

        /****************************** Email */
        if ($type != "1") {
            $("#perfil_validate").html("* O perfil obrigatório!");
        } else {
            $("#perfil_validate").html("*");
        }
        alert($type);


    }
</script>