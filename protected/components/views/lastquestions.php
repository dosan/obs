<ul>
<?php 
$questions = $this->getRecent();
 
foreach($questions as $question)
{
	echo '<li>'.CHtml::link($question->title,array('/questions/view/'.$question->id)).'</li>' ;
}
?>
</ul>