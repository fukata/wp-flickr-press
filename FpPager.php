<?php
class FpPager {
	private $page;
	private $total;
	private $perPage;
	private $htmlCache = '';

	public function __construct($total, $page=1, $perPage=20) {
		$this->total = $total;
		$this->page = $page;
		$this->perPage = $perPage;
	}

	public function generate($force=false) {
		if (!$force && strlen($htmlCache)>0) {
			return $htmlCache;
		}

		$html = paginate_links(array(
			'base' => add_query_arg('page', '%#%'),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil($this->total / $this->perPage),
			'current' => $this->page
		));
		$html = "<div class='tablenav-pages'>{$html}</div>";
		//$html = "<div class='fp_pager'>".$html."</div>";
		$this->htmlCache = $html;
		return $html;
	}
}
?>
