<?php
// /custom/leftmenu/index.php
require '../../main.inc.php';

$title = 'LeftMenu – Pregled';
llxHeader('', $title);
?>
<style>
.lm-wrap .lm-grid{display:grid;grid-template-columns:220px 1fr;gap:12px;align-items:start}
.lm-wrap .lm-sidebar{background:#fff;border:1px solid #e5e5e5;border-radius:6px;padding:8px}
.lm-wrap .lm-brand{font-weight:600;font-size:14px;color:#444;margin:4px 6px 8px}
.lm-wrap .lm-link{display:block;padding:8px 10px;border-radius:4px;color:#444;text-decoration:none}
.lm-wrap .lm-link:hover{background:#f6f7f9}
.lm-wrap .lm-link.active{background:#eef2ff;border:1px solid #dfe6ff}
.lm-wrap .lm-content{background:#fff;border:1px solid #e5e5e5;border-radius:6px;padding:12px}
@media(max-width:1200px){.lm-wrap .lm-grid{grid-template-columns:1fr}}
</style>

<div class="fiche lm-wrap">
  <div class="fichecenter">
    <div class="lm-grid">
      <aside class="lm-sidebar">
        <div class="lm-brand">LeftMenu</div>
        <nav>
          <a href="<?php echo dol_buildpath('/custom/leftmenu/index.php',1); ?>" class="lm-link active">Pregled</a>
          <a href="<?php echo dol_buildpath('/custom/leftmenu/items.php',1); ?>" class="lm-link">Stavke</a>
          <a href="<?php echo dol_buildpath('/custom/leftmenu/settings.php',1); ?>" class="lm-link">Postavke</a>
        </nav>
      </aside>

      <main class="lm-content">
        <h1 style="margin-top:0">LeftMenu – Pregled</h1>
        <p>Minimalni, čisti modul. Globalni Dolibarr meni ostaje netaknut.</p>
      </main>
    </div>
  </div>
</div>

<?php
llxFooter();
$db->close();