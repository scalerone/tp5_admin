<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model\page ;

use think\paginator\driver\Bootstrap;
use think\Paginator;

class Mypage extends Bootstrap
{

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '下一页')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }
    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple)
            return '';

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];
        $html = '';

        $html .= $this->getButton(Paginator::url(1),1);
        $html .= $this->getPreviousButton();
        $html .= '<li>
                <em>到第</em><input type="text" id="gotoPage" class="gotoPage" title="请输入要跳转到的页数并回车" name="gotopage" data-total="'.$this->lastPage.'" value="'.$this->currentPage.'"><em>
                /'.$this->lastPage.'&nbsp;页&nbsp;&nbsp;
                </em></li>';
        $html .=$this->getNextButton();
        $html .= $this->getButton(Paginator::url($this->lastPage),0);

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class="pager">%s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '<ul class="pagination">%s</ul>',

                    $this->getLinks()
                );
            }
        }
    }
    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getButton($url,$page)
    {
        if($page== 1){

        return '<li><a href="' . htmlentities($url) . '">首页</a></li>';
        }else{
        return '<li><a href="' . htmlentities($url) . '">尾页</a></li>';
        }
    }
//    protected function getAvailablePageWrapper($url, $page)
//    {
//        if($page==1){
//
//            return '<li><a href="' . htmlentities($url) . '">首页</a></li>';
//        }else if($this->lastPage()==$page){
//            return '<li><a href="' . htmlentities($url) . '">尾页</a></li>';
//        }else{
//
//            return '<li><a href="' . htmlentities($url) . '">'.$page.'</a></li>';
//        }
//    }
}