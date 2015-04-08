<?php
require_once(dirname(__FILE__) . "/src/tpls/head_tpl.html");
require_once(dirname(__FILE__) . "/src/tpls/header_tpl.html");
?>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/doc_dir.css"/>
<div id="wrapper">
    <div class="main-body-grid container">
        <div class="row" style="height: 100%">

            <div class="col-md-6 grid-left">
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
            <div class="col-md-6 row">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">Issues</h3>
                    </div>
                    <div class="panel-body list-group">
                            <a href="#" class="list-group-item">函数生成url中需要忽略DEFAULT_MODULE路径</a>
                            <a href="#" class="list-group-item">parseTemplate 使用定义常量TMPL_PATH</a>
                            <a href="#"
                               class="list-group-item">使用命名空间，导致IteratorAggregate接口未找到</a>
                            <a href="#" class="list-group-item">更改目录结构导致服务器500错误</a>
                    </div>
                </div>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Commits</h3>
                    </div>
                    <div class="panel-body list-group">
                        <a href="#" class="list-group-item">函数生成url中需要忽略DEFAULT_MODULE路径</a>
                        <a href="#" class="list-group-item">parseTemplate 使用定义常量TMPL_PATH</a>
                        <a href="#"
                           class="list-group-item">使用命名空间，导致IteratorAggregate接口未找到</a>
                        <a href="#" class="list-group-item">更改目录结构导致服务器500错误</a>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pull Requests</h3>
                    </div>
                    <div class="panel-body list-group">
                        <a href="#" class="list-group-item">函数生成url中需要忽略DEFAULT_MODULE路径</a>
                        <a href="#" class="list-group-item">parseTemplate 使用定义常量TMPL_PATH</a>
                        <a href="#"
                           class="list-group-item">使用命名空间，导致IteratorAggregate接口未找到</a>
                        <a href="#" class="list-group-item">更改目录结构导致服务器500错误</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? ?>
<?php require_once(dirname(__FILE__) . "/src/tpls/footer_tpl.html"); ?>
</html>