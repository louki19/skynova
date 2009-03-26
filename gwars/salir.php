<?php
include('basePage.php');
?>
<script language="JavaScript" type="text/javascript">

var pagina="./login"
function redireccionar() 
{
location.href=pagina
} 
setTimeout ("redireccionar()", 3500);

</script>
      <div align="center">
        <table class="generalTable">
          <tr>
            <td width="282" height="55" style="vertical-align:middle;"><div align="center"><span class="textoNormal">&iexcl;Hasta luego!</span></div></td>
          </tr>
          <tr>
            <th><p align="center" class="textoNormal">Gracias por jugar a Galactic Wars</p></th>
          </tr>
        </table>
      </div>
<?php
Session_destroy();
?>
