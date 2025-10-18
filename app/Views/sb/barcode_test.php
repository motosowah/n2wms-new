<h3>Barcode Scan Test</h3>
<form method="GET" action="">
    <input type="text" name="barcode" value="<?= esc($barcode ?? '') ?>" style="width: 100%;" placeholder="Scan barcode here..." />
    <hr/>
    <input type="submit" value="Submit" />
</form>
<pre><?= esc($data ?? '') ?></pre>