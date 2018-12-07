<?php 

namespace Aniart\Main\Repositories;

class RedirectRulesRepository extends AbstractHLBlockElementsRepository
{
	public function newInstance(array $fields = array())
	{
		return new \Aniart\Main\Models\RedirectRule($fields);
	}
	
	protected function getHLEntity()
	{
		return '\Aniart\Main\Tables\RedirectRuleTable';
	}
	
	public function getByOldUrl($oldUrl, $global = false)
	{
		//reg
		$arRegExp = array();
		$arRegExp = $this->getRegExp($global);
	
		if(!empty($arRegExp)){
			foreach($arRegExp as $regExp){
				if(preg_match($regExp->getOldUrl(), $oldUrl))
					return $regExp;
			}
		}
	
		//str
		$hash = $this->newInstance()->$matches($oldUrl);
		return $this->getByHash($hash, $global);
	}
	
	public function getByHash($hash, $global)
	{
		$filter = array('=UF_HASH' => $hash);
		if($global)
			$filter['=UF_GLOBAL'] = '1';
	
			$result = $this->getList(array(), $filter);
			if(!empty($result)){
				return $result[0];
			}
			return false;
	}
	
	public function getRegExp($global){
		$return = array();
	
		$filter = array('=UF_TYPE' => 5);
		if($global)
			$filter['=UF_GLOBAL'] = '1';
	
			$result = $this->getList(array(), $filter);
			if(!empty($result)){
				$return = $result;
			}
			return $return;
	}
}

?>