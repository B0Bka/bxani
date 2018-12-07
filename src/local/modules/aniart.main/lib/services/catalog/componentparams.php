<?
namespace Aniart\Main\Services\Catalog;
use \Aniart\Main\Ajax\Handlers\CatalogAjaxHandler;

class ComponentParams extends CatalogAjaxHandler{

	public function rewriteParams($params, $additionalParams, $setPrice){
		if($setPrice != true){
			$new_filter = array("PROPERTY_".$additionalParams['code'] => $additionalParams['value']);
		} else {
			$new_filter = array(
				">=PROPERTY_".PROP_MIN_PRICE_ID =>$additionalParams['value']['min'],
				"<=PROPERTY_".PROP_MIN_PRICE_ID =>$additionalParams['value']['max'],
			);
		}

		$params_count = 0;
		$section_exists = false;
		foreach ($params["FILTER"] as $key=>$value){
			if(gettype($key) != 'integer') continue;
			$params_count ++ ;
			foreach ($value as $k=>$subArray) {
				if (gettype($k) != 'integer') continue;

				foreach ($subArray as $subkey => $subvalue) {

//					удаление
					if($subkey == "PROPERTY_".$additionalParams['code'] && $subvalue == $additionalParams['value']){
						$set = false;
						$key_to_unset = $k;
						$key_to_unset_parent = $key;
					}

//					Добавление К РАЗДЕЛУ
					if($setPrice != true) {
						if ($subkey == "PROPERTY_" . $additionalParams['code'] && in_array($new_filter, $value) == false) {
							$set = true;
							$key_to_set = $k;
							$key_to_set_parent = $key;
						}
					} else {
						if ($subkey == ">=PROPERTY_" . $additionalParams['code'] && in_array($new_filter, $value) == false) {
							$set = true;
							$key_to_set = $k;
							$key_to_set_parent = $key;
						}
					}

//					Добавление РАЗДЕЛА
					if($setPrice != true) {
						if ($subkey == "PROPERTY_" . $additionalParams['code']) {
							$section_exists = true;
						}
					} else {
						if ($subkey == ">=PROPERTY_" . $additionalParams['code'] || $subkey == "<=PROPERTY_" . $additionalParams['code']) {
							$section_exists = true;
						}
					}
				}
			}
		}

//	    Добавление РАЗДЕЛА
		if($section_exists == false){
			if($setPrice != true) {
				$params["FILTER"][$params_count] = Array("LOGIC" => "OR", 0 => $new_filter);
			} else {
				$params["FILTER"][$params_count] = Array("LOGIC" => "AND", 0 => $new_filter);
			}
		}

		if($params_count == 0){
			if($setPrice != true) {
				$params["FILTER"]["0"] = Array("LOGIC" => "OR", 0 => $new_filter);
			} else {
				$params["FILTER"]["0"] = Array("LOGIC" => "AND", 0 => $new_filter);
			}
		}
		if($set){
			if($setPrice != true) {
				$params["FILTER"][$key_to_set_parent][$key_to_set + 1] = $new_filter;
			} else {
				$params["FILTER"][$key_to_set_parent][$key_to_set] = $new_filter;
			}
		} else {
			unset($params["FILTER"][$key_to_unset_parent][$key_to_unset]);
			if(count($params["FILTER"][$key_to_unset_parent]) == 1 && gettype($params["FILTER"][$key_to_unset_parent][0]) !== 'integer'){ //если остался только Array([LOGIC] => OR) - удаление пустого раздела
				unset($params["FILTER"][$key_to_unset_parent]);
			}
		}

		return $params;
	}

	private function getBasicFilterParams($params){
		if(isset($params["FILTER"])){
			foreach ($params["FILTER"] as $key => $val){
				if(gettype($key) === 'integer') {
					unset($params["FILTER"][$key]);
				}
			}
		}
		return $params["FILTER"];
	}

	public function rewriteFilterParamsByOnce($params, $additionalParams){

		$params["FILTER"] = self::getBasicFilterParams($params);
		$i = 0;
		foreach ($additionalParams["properties"] as $key=>$value){
			if($key == "min_price"){
				$params["FILTER"][$i] = Array("LOGIC"=>"AND");
				foreach ($additionalParams["properties"][$key] as $k => $filter){
					$params["FILTER"][$i][] = Array(
						">=PROPERTY_".$filter["property_section_id"] => intval($filter["value"]["min"]),
						"<=PROPERTY_".$filter["property_section_id"] => intval($filter["value"]["max"]),
						);
				}
			} else {
				$params["FILTER"][$i] = Array("LOGIC"=>"OR");
				foreach ($additionalParams["properties"][$key] as $k => $filter){
					$params["FILTER"][$i][] = Array("PROPERTY_".$filter["property_section_id"] => $filter["value"]);
				}
			}

			$i++;
		}
		return $params;
	}
}
?>