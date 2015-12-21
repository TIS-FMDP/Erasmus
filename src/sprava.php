<?php

?>

<!DOCTYPE HTML>

<html>
<?php



if(isset($_POST["uloz"])){
    echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>vlozil som";
   
}
?>
<form method=POST>
    <div class="spravaVykonu">
        <h2>Spravovanie výkonu:</h2>
        <table style="width:100%;">
            <tr>
                <td class="table-left"><label>Meno súťažiaceho:</label></td>
                <td class="table-right"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Preteky:</label></td>
                <td class="table-right"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Dátum:</label></td>
                <td class="table-right"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Miesto:</label></td>
                <td class="table-right"><input type="text" name="MIESTO" id="MIESTO"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Víťaz:</label></td>
                <td class="table-right"><input type="text" name="VITAZ" id="VITAZ"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Čas víťaza:</label></td>
                <td class="table-right"><input type="text" name="VITAZ_CAS" id="VITAZ_CAS"></td>
            </tr>
            <tr>
                <td class="table-left"<label>Môj čas:</label></td>
                <td class="table-right"><input type="text" name="MOJ_CAS" id="MOJ_CAS"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Nabehaná vzdialenosť v km:</label></td>
                <td class="table-right"><input type="text" name="VZDIALENOST" id="VZDIALENOST"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Ideálna vzdialenosť v km:</label></td>
                <td class="table-right"><input type="text" name="IDEAL_VZDIALENOST" id="IDEAL_VZDIALENOST"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Rýchlosť min/km:</label></td>
                <td class="table-right"><input type="text" name="RYCHLOST" id="RYCHLOST"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Prevýšenie m/km:</label></td>
                <td class="table-right"><input type="text" name="PREVYSENIE" id="PREVYSENIE"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Odchýlka nabehané/ideálne mínus 1(%):</label></td>
                <td class="table-right"><input type="text" name="ODCHYLKA" id="ODCHYLKA"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Prirážka % v závislosti od kopcov a rýchlosti:</label></td>
                <td class="table-right"><input type="text" name="PRIRAZKA" id="PRIRAZKA"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Hodnotiace kritérium %:</label></td>
                <td class="table-right"><input type="text" name="HODNOTENIE" id="HODNOTENIE"></td>
            </tr>
            <tr>
                <td class="table-left"></td>
                <td class="table-right"><input type=submit name="uloz" id="uloz" value="Ulož" /></td>
            </tr>
        </table>
    </div>
</form>
    <br><br><br>




</html>

