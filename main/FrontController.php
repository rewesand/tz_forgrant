<?php

class FrontController extends \Controller {

	public $template = 'Front';
	public $page_title_prefix = 'BIQ Developer';
	public $meta_description = FALSE;
	public $meta_keywords = FALSE;
	public $main_menu_show = true;
	
	public $content	= '';
	
	protected $auto_render = TRUE;
	
	public function before() {
		$is_ajax = $this->is_ajax = $this->request->isAjax();
		
		if ($this->auto_render === TRUE && !$is_ajax){
			$this->template = \View::factory($this->template);
		} else {
			$this->auto_render = false;
		}
	}
	
	public function after() {
		$this->template->content = $this->content;
		$this->template->page_title_prefix = $this->page_title_prefix;
		$this->template->meta_description = $this->meta_description;
		$this->template->meta_keywords = $this->meta_keywords;
		$this->template->main_menu_show = $this->main_menu_show;
		$this->template->request = $this->request;
		
		if ($this->auto_render === TRUE){
			$this->request->body($this->template->render());
        } else {
            $this->request->body($this->content);
        }
	}
}
