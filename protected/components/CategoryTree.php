<?php 	
/**
* Category tree adjacency
*/
class CategoryTree extends CPortlet
{

	private static $menuTree = array();

	public static function getMenuTree() {
		if (empty(self::$menuTree)) {
			$rows = AdvertsCategories::model()->findAll('parent_id = 0');
			foreach ($rows as $item) {
				self::$menuTree[] = self::getMenuItems($item);
			}
		}
		return self::$menuTree;
	}

	private static function getMenuItems($modelRow) {

		if (!$modelRow)
			return;

		if (isset($modelRow->Childs)) {
			$chump = self::getMenuItems($modelRow->Childs);
			if ($chump != null)
				$res = array('label' => $modelRow->title, 'items' => $chump, 'url' => Yii::app()->createUrl('adverts/category', array('id' => $modelRow->id)));
			else
				$res = array('label' => $modelRow->title, 'url' => Yii::app()->createUrl('adverts/category', array('id' => $modelRow->id)));
			return $res;
		} else {
			if (is_array($modelRow)) {
				$arr = array();
				foreach ($modelRow as $leaves) {
					$arr[] = self::getMenuItems($leaves);
				}
				return $arr;
			} else {
				return array('label' => ($modelRow->title), 'url' => Yii::app()->createUrl('adverts/category', array('id' => $modelRow->id)));
			}
		}
	}
}