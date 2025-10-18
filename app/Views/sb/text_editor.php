<h3>Plain Text Editor</h3>
<form method="POST" action="save_text">
    <textarea name="content" style="width: 100%; height: 400px;"><?= esc($content ?? '') ?></textarea>
    <hr/>
    <input type="submit" value="Save" />
</form>