<?php
require "php/config.php";
require "php/db.php";
require "php/com.solvemedia/solvemedia.php";
if(!$open){header("HTTP/2.0 403 Forbidden");exit("<center><h1>403 Forbidden</h1></center><hr>");}else{header("X-ListedShop-Version: 0");}
?>
<?php
if((!isset($_GET['name'])||!isset($_GET['id']))&&!isset($_GET['api'])){
    header("HTTP/2.0 307 Temporary Redirect");
    header("Location: /");
}
$db=new DBOperator($dbHost,$dbName,$dbUser,$dbPassword);
$mod=$db->_getModById($_GET['id']);
if(!$mod&&isset($_GET['api'])){
    header("HTTP/2.0 404 Not Found");
    echo "<Error code=\"041\">Not Found</Error>";
    exit;
} else if(!$mod) {
    header("HTTP/2.0 404 Not Found");
    echo file_get_contents("error/404.html");
    exit;
}
if(isset($_GET['api'])&&$_GET['api']==="xml") {
    $icon="/fores/icons/{$mod["id"]}.png";
    $description;
    if(file_exists("fores/descs/{$mod["id"]}.xml")) {
      $description=file_get_contents("fores/descs/{$mod["id"]}.xml");
    } else {
        $description="<Error code=\"047\">not available yet</Error>";
    }
    if(file_exists("fores/dl/{$mod["id"]}")) {
        $files=array_diff(scandir("fores/dl/".$mod["id"]), array('..', '.'));
        $downloads="";
        foreach($files as $file) {
          $downloads.="<Downloads name=\"{$file}\">\n<File>/download.php?id={$mod["id"]}&file={$file}</File>\n</Downloads>";
        }
    } else {
        $downloads="<Error code=\"047\">not available yet</Error>";
    }
    echo "<Mod>\n<Title>{$mod["tid"]}</Title>\n<Icon>{$icon}</Icon>\n<Description lang=\"ru-RU\">\n{$description}\n</Description>\n{$downloads}\n</Mod>";
    exit;
}
?>
<!DOCTYPE html>
<html data-ng-app="mview">
  <head>
    <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,700|EB+Garamond&amp;subset=cyrillic-ext" rel="stylesheet">
    <link rel="stylesheet" href="/skin/default.css"></link>
    <link rel="stylesheet" href="/skin/slimbox2.css"></link>
    <script src="/js/jquery.js"></script>
    <script src="/js/mustache.js"></script>
    <script src="/js/slimbox2.js"></script>
    <title><?php echo $mod["tid"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="моды майнкрафт PE minecraft mods скачать <?php echo "скачать {$mod['tid']} бесплатно скачать {$mod['tid']} {$mod['tid']}"; ?>"/>
  </head>
  <body>
    <div id="header">
      <a href="/"><img src="<?php echo $logo; ?>" class="logo"/></a>
      <div id="menu">
      </div>
    </div>
    <div id="filters">
      <h2 class="no-margin" >Скачать</h2>
      <?php
        if(file_exists("fores/dl/".$mod["id"])) {
          $files=array_diff(scandir("fores/dl/".$mod["id"]), array('..', '.'));
          foreach($files as &$file) {
            echo "<a class='downloadable' href='/download.php?id=".$mod["id"]."&file=".$file."'><img src='/skin/icons8/download-one.png' alt='скачать мод' />&nbsp;&nbsp;".$file."</a>";
          }
        } else {
            echo "<p>Загрузки для этого файла пока недоступны.<br/>MID: ".$mod["id"]."</p>";
        }
      ?>
    </div>
    <div id="contentpane">
    <div id="modcart" class="map">
      <div class="mod-poster"><!-- to be implemented-->
        <h1 class="name no-margin"><?php echo $mod["tid"]; ?></h1>
      </div>
      <div class="description">
        <p>
          <?php 
            if(file_exists("fores/descs/".$mod["id"].".xml")) {
              echo file_get_contents("fores/descs/".$mod["id"].".xml");
            } else {
                echo "<p>Описание этого файла временно недоступно.</p>";
            }
          ?>
        </p>
      </div>
    </div>
    <div id="comments_section" class="map" >
      <h2>Комментарии</h2>
      <form method="POST" action="/comment/<?php echo $mod["id"]; ?>/" enctype="multipart/form-data">
        <fieldset>
          <legend>Добавить комментарий</legend>
          <a href="/faq/howto-comment.html" target="_blank">Прочитайте внимательно правила комментирования :)</a><br/><br/>
          <label>Ваше имя: </label> <input type="text" name="name" /><br/><br/>
          <strong>Поддерживается HTML5. (<a href="/faq/howto-comment.html#markup" target="_blank">руководство</a>).</strong><br/>
          <label>Ваш комментарий: </label><br/><textarea id="comment_body" rows="5" name="commentBody"></textarea><br/><br/>
          <hr/>
          <div class="comment-preview commentary book" style="padding: 5px;"></div><br/><br/>
          <?php echo solvemedia_get_html($smckey);?>
          <input type="submit" value="Ответить"/>
        </fieldset>
      </form>
      <hr/>
      <div class="comments-list"></div>
      <ins id="moreComments">Больше комментариев</ins>
    </div>
    </div>
    <script id="CommentTemplate" type="x-tmpl-mustache">
      <div class="comment-container book">
        <a name="-comment-{{uuid}}"></a>
        <div class="comment-info">
         <p class="no-margin author" style="font-size: 24px;">{{from}}</p>
        </div>
        <div class="commentary comment-body">{{content}}</div>
      </div>
    </script>
    <script>
    $('#modcart img').each( function() {
      var $img = $(this), href = $img.attr('src');
      $img.wrap('<a rel="lightbox" href="' + href + '" class="zoom" title="<?php echo $mod["tid"]; ?>" ></a>');
    });
    $("#comment_body").on("input propertychange",function(){
        $("#comment_body").val($("#comment_body").val().replace(/h(1|2|3)/gmui,'h4').replace(/(script|iframe|link|embed|object|applet|div)|(<style>|<\/style>)/gmui,'запрещённый элемент').replace(/strike/gmui,'s').replace(/((хуй|сука|бля(т|д)ь|(е|ё)бать|пид(о|o|а)рас|пид(a|а|o|о)р|хуесос|пизда|педераст|nier|(е|ё)б|елда|нахуй|ублюдок|мудак|залупа|г(а|o|о)ндон|манда|пидр|ублюдок|шлюха|падла|трах)|(huy|suka|blya(t|d)|(e|Yo)bat|pid(o|o|a)s|huesos|pizda|pederast|nier|elda|nahuy|ublyudok|mudak|zalupa|g(a|o|o)ndon|manda|pidr|pid(а|о)|ublyudok|shlyuha|padla|tra(h|x)))/gmui, '▓▓▓▓▓').replace(/(fuck|retard|asshole|assbag|douchebag|(cock|dick)|sucker|nerd|loser|prick|dick|cunt|slut|whore|hoe|bastard|jerk|bitch|bullshit|motherfucker)/gmui,'▓▓▓▓▓')); //sanitize
        $(".comment-preview").html($("#comment_body").val());
    });
</script>
  <script>
  $("#moreComments").hide();
  const commentTemplate=$("#CommentTemplate").html();
    const perPage=<?php echo $itemsPerPage; ?>;
    var offset=0;
    function importCommentsFromArray(array) {
        Mustache.parse(commentTemplate);
        var newItems="";
        array.forEach(function(comment){
            newItems+=Mustache.render(commentTemplate,comment);
        });
        $(".comments-list").append(newItems);
        if(array>=perPage)
            $("#moreComments").show();
        else if(array<perPage)
            $("#moreComments").hide();
    }
    function moreComments() {
      $.ajax({
          url: '/comment/'+offset+'/<?php echo $mod["id"]; ?>/'+perPage,
          success: function(out) {
              offset+=perPage;
              importCommentsFromArray(JSON.parse(out));
          }
      });
    }
    moreComments();
  </script>
  </body>
</html>