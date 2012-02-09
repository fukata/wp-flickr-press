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
		if (!$force && strlen($this->htmlCache)>0) {
			return $this->htmlCache;
		}

		$html = paginate_links(array(
			'base' => add_query_arg('paged', '%#%'),
			'format' => '',
			'prev_text' => __('&laquo;', FlickrPress::TEXT_DOMAIN),
			'next_text' => __('&raquo;', FlickrPress::TEXT_DOMAIN),
			'total' => $this->total > 0 ? ceil($this->total / $this->perPage) : 0,
			'current' => $this->page
		));
		$html = "<div class='tablenav-pages'>{$html}</div>";
		$this->htmlCache = $html;
		return $html;
	}
}
?>
