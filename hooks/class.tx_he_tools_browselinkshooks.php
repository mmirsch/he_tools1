<?php

if (!defined ('TYPO3_MODE'))
	die ('Access denied.');


class tx_he_tools_browselinkshooks  {

	/*
	 * returns additional params
	 *
	 * @param	array			additional parameters
	 * @param	browse_links	parent browse_links object
	 * @return	void
	 */
	public function getAttributefields(&$additionalParameters,&$parentObject) {
		$act = $parentObject->curUrlInfo['act'];
		$pageid = $parentObject->curUrlInfo['pageid'];
		if ($act=='url' || $act=='page') {
			return '
						<tr>
							<td>Seiten-ID:</td>
							<td colspan="3">
								<input type="text" id="typo3PageId" name="typo3PageId" value="' . $pageid . '"  ' . $parentObject->doc->formWidth(8) . ' />
								<input type="submit" value="Link auf Seiten-ID setzen" onclick="return link_pageId();">
							</td>
						</tr>';
				} else {
					return '';
				}
		}
		
	
	/*
	 * extendJScode
	 *
	 * @param	array			additional parameters
	 * @param	browse_links parent browse_links object
	 * @return	void
	 */
	public function extendJScode(&$additionalParameters,&$parentObject) {
		return '
		function link_pageId()	{
				var parameters = (document.ltargetform.query_parameters && document.ltargetform.query_parameters.value) ? (document.ltargetform.query_parameters.value.charAt(0) == "&" ? "" : "&") + document.ltargetform.query_parameters.value : "";
				var pageId = document.ltargetform.typo3PageId.value;
				var pageIdClean = parseInt(pageId);
				if (isNaN(pageId) || !isFinite(pageId) || pageId==0 || pageId!=pageIdClean) {
					alert("Bitte geben Sie eine gültige Seiten-Id ein!");
				} else {
					var href = "https://www.hs-esslingen.de/?id=" + pageId;
					if (document.ltargetform.anchor_title) browse_links_setTitle(document.ltargetform.anchor_title.value);
					if (document.ltargetform.anchor_class) browse_links_setClass(document.ltargetform.anchor_class.value);
					if (document.ltargetform.ltarget) browse_links_setTarget(document.ltargetform.ltarget.value);
					if (document.ltargetform.lrel) browse_links_setAdditionalValue("rel", document.ltargetform.lrel.value);
					plugin.createLink(href + parameters,cur_target,cur_class,cur_title,additionalValues);
				}				
				return false;
		}';
	}
	
	
}


?>