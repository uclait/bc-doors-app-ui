<?php
$conn = oci_connect("ENVISION", "B8_bc_db_p01_EN", "BC-DB-P01.DPNS.AIS.UCLA.EDU:1521/BBTS");
$stid = oci_parse($conn, 'select TABLE_NAME from cat');

oci_execute($stid);
echo "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS))
{
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";
oci_free_statement($stid);
oci_close($conn);
?>
