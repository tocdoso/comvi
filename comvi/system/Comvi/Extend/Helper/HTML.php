<?php
namespace Comvi\Extend\Helper;

/**
 * HTML helper class.
 *
 * @static
 * @package		Comvi.Extend
 * @subpackage	Helper
 */
class HTML
{
	/**
	 * Converts your text to a URL-friendly title so it can be used in the URL.
	 * Only works with UTF8 input and and only outputs 7 bit ASCII characters.
	 *
	 * @param   string  the text
	 * @param   string  the separator (either - or _)
	 * @return  string  the new title
	 */
	public static function parseTableOfContents(&$content/*, $depth = 3*/)
	{
		$return = preg_match_all('/<h([1-6])(.*)>([^<]+)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER);

		if ($return === 0) {
			return null;
		}

		$anchors = array();
		$toc 	 = '<ol class="toc">'."\n";
		$i 		 = 0;

		foreach ($matches as $heading) {
			if ($i == 0) {
				$startlvl = $heading[1];
			}
			$lvl = $heading[1];

			$ret = preg_match('/id=[\'|"](.*)?[\'|"]/i', stripslashes($heading[2]), $anchor);
			if ($ret && $anchor[1] != '') {
				$anchor = stripslashes($anchor[1]);
				$add_id = false;
			}
			else {
				//$anchor = preg_replace('/\s+/', '-', preg_replace('/[^a-z\s]/', '', strtolower($heading[3])));
				$anchor = URL::title($heading[3]);
				$add_id = true;
			}

			if (!in_array($anchor, $anchors)) {
				$anchors[] = $anchor;
			}
			else {
				$orig_anchor = $anchor;
				$i = 2;
				while (in_array($anchor, $anchors)) {
					$anchor = $orig_anchor.'-'.$i;
					$i++;
				}
				$anchors[] = $anchor;
			}

			if ($add_id) {
				$content = substr_replace(
					$content, '<h'.$lvl.' id="'.$anchor.'"'.$heading[2].'>'.$heading[3].'</h'.$lvl.'>',
					strpos($content, $heading[0]),
					strlen($heading[0])
				);
			}

			$ret = preg_match('/title=[\'|"](.*)?[\'|"]/i', stripslashes($heading[2]), $title);
			if ( $ret && $title[1] != '' ) {
				$title = stripslashes($title[1]);
			}
			else {
				$title = $heading[3];
			}
			$title = trim(strip_tags($title));

			if ($i > 0) {
				if ($prevlvl < $lvl) {
					$toc .= "\n"."<ol>"."\n";
				}
				elseif ($prevlvl > $lvl) {
					$toc .= '</li>'."\n";
					while ($prevlvl > $lvl) {
						$toc .= "</ol>"."\n".'</li>'."\n";
						$prevlvl--;
					}
				}
				else {
					$toc .= '</li>'."\n";
				}
			}
	 
			$j = 0;
			$toc .= '<li><a href="#'.$anchor.'">'.$title.'</a>';
			$prevlvl = $lvl;
	 
			$i++;
		}

		while ($lvl > $startlvl) {
			$toc .= "\n</ol>";
			$lvl--;
		}

		$toc .= '</li>'."\n";
		$toc .= '</ol>'."\n";

		return $toc;
	}

	public static function parseTOC(&$html_string/*, $depth = 3*/)
	{
		return static::parseTableOfContents($html_string/*, $depth*/);
	}
}
?>