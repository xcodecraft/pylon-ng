<?php
// 当文件不存在时，跳转主页
$documentUrl = substr($_SERVER['DOCUMENT_URI'], 1) . ".md";
if (!file_exists($documentUrl)) {
    header("Location: /");
    die();
}
require_once(dirname(__FILE__) . "/src/tpls/head_tpl.html");
?>

<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/docs.css"/>

<div id="wrapper">
    <!--  header  -->
    <?php require_once(dirname(__FILE__) . "/src/tpls/header_tpl.html") ?>
    <!--  main  -->
    <div role="main" class="container main-body">
        <div class="main-grid main-body-grid">
            <div class="grid-left sidebar">
                <?php
                require_once('src/dir_tree.php');
                try {
                    $fileData = DirTree::fillDomWithFileNodes(new DirectoryIterator(('doc/')));
                    $dom = new DomDocument("1.0");
                    $dom->appendChild($dom->importNode($fileData, true));
                    echo $dom->saveHtml();
                } catch (Exception $e) {
                    var_dump($e);
                }
                ?>
            </div>
            <div class="grid-right">
                <?php
//                require_once 'libs/php_markdown/Michelf/Markdown.inc.php';
                require_once 'libs/php_markdown/Michelf/MarkdownExtra.inc.php';
                $documentUrl = substr($_SERVER['DOCUMENT_URI'], 1) . ".md";
                $text = file_get_contents($documentUrl);
//                $html = Michelf\Markdown::defaultTransform($text);
                $html = Michelf\MarkdownExtra::defaultTransform($text);
                echo $html;
                ?>
            </div>
        </div>

    </div>
    <!-- footer  -->
    <?php require_once(dirname(__FILE__) . "/src/tpls/footer_tpl.html"); ?>
<!--    <link rel="stylesheet" href="/css/tomorrow-night.css">-->
        <link rel="stylesheet" href="/css/monokai.css">
    <script src="/js/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
</div>
</html>
