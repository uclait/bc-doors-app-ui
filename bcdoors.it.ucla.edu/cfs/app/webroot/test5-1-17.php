<?php
$conn = oci_connect("bcapp", "test##_write_bcuser", "bc-db-t01.dpns.ais.ucla.edu:1521/BCDOORST");
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
