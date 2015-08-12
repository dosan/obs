<ul>
<?php 
$adverts = $this->getRecent();
 
foreach($adverts as $advert)
{
	echo '<li>'.CHtml::link($advert->title,array('/adverts/view', 'id'=>$advert->id)).'</li>' ;
}
?>
</ul>