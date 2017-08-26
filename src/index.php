<?php
require "php/config.php";
require "php/db.php";
if(!$open){header("HTTP/2.0 403 Forbidden");exit("<center><h1>403 Forbidden</h1></center><hr>");}else{header("X-ListedShop-Version: 0");}
?>
<?php
if(!isset($_GET["verpreset"])){$version=true;}else{if($_GET["verpreset"]=="true"){$version=true;}else{$version=$_GET["verpreset"];}} #lolwut
if(!isset($_GET["cat"])){$category=true;}else{if($_GET["cat"]=="true"){$category=true;}else{$category=$_GET["cat"];}}
$snooze=true;
if(!isset($_COOKIE['snooze_bug_report_prompt'])) {
	$snooze=false;
	setcookie("snooze_bug_report_prompt",true,time()+1990000);
}
$db=new DBOperator($dbHost,$dbName,$dbUser,$dbPassword);
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,700|EB+Garamond&amp;subset=cyrillic-ext" rel="stylesheet">
    <script src="js/jquery.js"></script>
    <link rel="stylesheet" href="/skin/default.css"></link>
    <title>Моды майнкрафт</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="моды майнкрафт PE minecraft mods скачать industrialcraft industrial craft"/>
  </head>
  <body>
    <div id="header">
      <a href="/"><img src="<?php echo $logo; ?>" class="logo" alt="2craft.net - моды для каждого." /><sup style="color: #fff;">β</sup></a>
      <div role="search" id="menu">
        <form method="POST" action="/search/">
          <input name="query" type="text" id="searchbar" placeholder="Поиск"/>
        </form>
      </div>
    </div>
	<div id="filters" role="search">
	    <div id="EditorsChoice">
	    <h3 class="no-margin"><img src="skin/icons8/best.png" />Выбор редакции</h3>
		<ol class="editors-choice no-margin">
		  <?php
		    foreach(array_slice($editorsChoice, 0, 5) as $choice) {
				$cho=$db->_getModById($choice); #chobject
				echo "<li><a href='/mod/{$cho["id"]}/".rawurlencode($cho["tid"])."'>{$cho["tid"]}</a></li>";
			}
		  ?>
		</ol>
		</div>
		<form method="GET" action="#search">
		<h3 class="no-margin"><img src="skin/icons8/filter.png" alt="Фильтры" />Фильтры</h3>
		<label for="order">Сортировать по</label><br/>
		<select disabled="disabled" id="order" name="order">
		  <option selected="selected" value="lastup">Сначала новые</option>
		  <!--<option value="lastup">Сначала старые</option>-->
		  <!--<option value="abc" selected>По алфавиту</option>-->
		</select><br/>
		<label for="cat" class="no-margin">Категории</label><br/>
		<select id="cat" name="cat">
		<?php
		  echo "<option value=\"true\">Не важно</option>";
		  foreach($cats as $cat) {
			  echo "<option value=\"".$cat."\">".$cat."</option>";
		  }
		?>
		</select><br/>
		<label for="verpreset">Совместимость с версиями</label><br/>
		<select id="verpreset" name="verpreset">
		<?php
		  echo "<option value=\"true\">Не важно</option>";
		  foreach($versions as $ver) {
			  echo "<option value=\"".$ver."\">".$ver."</option>";
		  }
		?>
		</select><br/>
		<button type="submit" id="search" class="black-button right"><p class='no-margin'><img alt="Кнопка поиска" src="/skin/icons8/search.png" />Поиск</p></button>
		<p id="VERSION"><br/>XLS v.0.0.1a<br/><img height="32" width="88" src="http://www.w3.org/WAI/wcag2AAA-blue" alt="Level Triple-A conformance, W3C WAI Web Content Accessibility Guidelines 2.0"><!-- СИЛЬНОЕ ЗАЯВЛЕНИЕ!1 -->&nbsp;&nbsp;<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Правильный CSS!" /></a></p>
		</form>
		<br/>
		<br/>
	  </div>
    <div id="contentpane">
	  <div id="list" class="map">
	  <?php echo "<!--".file_get_contents("skin/announce.xml")."-->"; ?>
	  <?php
	    if(!isset($_GET['page'])){$page=0;}else{$page=$_GET['page']-1;}
	    $offset=$itemsPerPage*$page;
		$ex=false;
		if(!isset($_POST['query'])){$searchFor=true;}else{$searchFor=$_POST['query'];}
		foreach($db->_getModsAsArray($offset,$itemsPerPage,$version,$category,$searchFor) as &$mod ){
			$ex=true;
			echo "<div class='modification'><a href='/mod/".$mod["id"]."/".rawurlencode($mod["tid"])."'><img alt='".$mod["tid"]."' class='mod-image' src='/fores/icons/".$mod["id"].".png'/><h2 class='no-margin'>".$mod["tid"]."</h2></a><div class='metainfo'><!--<p>Скачиваний: ".$mod["downloads"]."</p>--><p>Обновлён: ".date("l d M o @ H:i.v A",$mod["updated"])."</p></div></div><br/>";
		}
		if(!$ex) {
			echo "<div class='404'><h1 class='no-margin'>Ошибка 404</h1><hr/><p>Не найдено модов по вашему запросу :(</p></div>";
		} else {
			if($page==0) {
				echo "<center><p>1&nbsp;<a href='?page=2'>&gt;&gt;</a></p></center>";
			} else {
			  echo "<center><p id='Paginator'><a href='?page=".($page-1)."'>&lt;&lt;</a>&nbsp;{$page}&nbsp;<a href='".($page+1)."'>&gt;&gt;</a></center>";
			}
		}
	  ?>
	  </div>
	</div>
	<?php if(!$snooze){echo "<div class=\"survey\" onclick=\"window.open('/issues.php','Отчёт об ошибке','width=345,height=550,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');\" id=\"IssueTracker\"><p>2craft находиться на стадии бета тестирования. Вы нам очень поможете если заполните опрос.</p></div>";}?>
  </body>
</html>