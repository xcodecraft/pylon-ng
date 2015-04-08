<?php

/**
 * Date: 2/9/15
 * Time: 17:53
 */

//$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

class DirTree
{
    /**
     * 这方法很厉害，备用
     * @param $iterator
     * @return DomDocument
     */
    public static function getDom($iterator)
    {
        $dom = new DomDocument("1.0");
        $list = $dom->createElement("ul");
        $dom->appendChild($list);
        $node = $list;
        $depth = 0;
        foreach ($iterator as $name => $object) {
            if ($iterator->getDepth() == $depth) {
            // the depth hasnt changed so just add another li
                $li = $dom->createElement('li', $object->getFilename());
                $node->appendChild($li);
            } elseif ($iterator->getDepth() > $depth) {
            // the depth increased, the last li is a non-empty folder
                $li = $node->lastChild;
                $ul = $dom->createElement('ul');
//                $ul->setAttribute('depth',$iterator->getDepth());
                $li->appendChild($ul);
                $ul->appendChild($dom->createElement('li', $object->getFilename()));
                $node = $ul;
            } else {
            // the depth decreased, going up $difference directories
                $difference = $depth - $iterator->getDepth();
                for ($i = 0; $i < $difference; $difference--) {
                    $node = $node->parentNode->parentNode;
                }
                $li = $dom->createElement('li', $object->getFilename());
                $node->appendChild($li);
            }
            $depth = $iterator->getDepth();
        }
        return $dom;
    }

    /**
     * 获取 Array 结构文件的数据
     * @param DirectoryIterator $dir
     * @return array
     */
    static function fillArrayWithFileNodes(DirectoryIterator $dir)
    {
        $data = array();
        foreach ($dir as $node) {
            if ($node->isDir() && !$node->isDot()) {
                $data[$node->getFilename()] = DirTree::fillArrayWithFileNodes(new DirectoryIterator($node->getPathname()));
            } else if ($node->isFile()) {
                $data[] = $node->getFilename();
            }
        }
        return $data;
    }

    /**
     * 获取 DOM 结构文件目录
     * @param DirectoryIterator $dir
     * @return DOMElement
     */
    static function fillDomWithFileNodes(DirectoryIterator $dir)
    {
        try {
            $dom = new DomDocument("1.0");
            $ul = $dom->createElement('ul');
            $ul->setAttribute("class", "nav nav-pills nav-stacked");
            foreach ($dir as $item) {
                if ($item->isDir() && !$item->isDot()) {
                    //文件夹
                    $a = $dom->createElement('a', $item->getFilename());
                    $li = $dom->createElement('li');
                    $li->appendChild($a);
                    $subul = DirTree::fillDomWithFileNodes(new DirectoryIterator($item->getPathname()));
//                    $subul->setAttribute("class", "nav nav-pulls nav-stacked");
                    $li->appendChild($dom->importNode($subul, true));
                    $ul->appendChild($li);
                } elseif ($item->isFile()) {
                    // 文件
                    $ext = pathinfo($item->getFilename(), PATHINFO_EXTENSION);
                    $filename = basename($item->getFilename(), '.' . $ext);
                    $a = $dom->createElement('a', $filename);
                    $a->setAttribute('href',"/" . $item->getPath() . "/" . $filename);
                    $li = $dom->createElement('li');
                    $li->appendChild($a);
                    $ul->appendChild($li);
                }
            }
            return $ul;
        } catch (Exception $e){

        }
    }


}