<?php

use yii\helpers\{Html,Url};

?>
<div class="docNotes">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-envelope"></i><span class="count"><?php echo $count;?></span></a>
    <?php if(count($notes)){?>
    <ul class="dropdown-menu dropdown-user">
        <?php foreach($notes as $gr => $ns){ ?>
        	<li class="docs">
        		<p><?php echo $gr;?></p>
        		<ul class="doc-items">
        			<?php foreach ($ns as $note) { ?>
		        		<li><?php echo $note->displayNote();?></li>
		            <?php }?>
        		</ul>
        	</li>
    	<?php } ?>
    </ul>
    <?php }?>            
</div>