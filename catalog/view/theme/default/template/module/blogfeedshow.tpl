<div class="box blogModule artHorz-3">
<div class="box-heading"><span><?php echo $heading_title; ?></span></div>

<div class="box-content">


<ul class="recentArticles">
<?php foreach ($feedentries as $feedentry): ?>
<li>

<?php if ($feedentry['image']!=''):?>
<a class="image" href="<?php echo $feedentry['link']; ?>" title=""><img alt="" src="<?php echo $feedentry['image']; ?>" /></a>
<?php endif; ?>
<a class="title" href="<?php echo $feedentry['link']; ?>" title=""><?php echo $feedentry['title']; ?></a>
<span class="info"><?php echo $feedentry['day']; ?>/<?php echo $feedentry['month']; ?>/<?php echo $feedentry['year']; ?></span>
<p><?php echo $feedentry['description']; ?></p>


</li>
<?php endforeach; ?>
</ul>

</div>

<div class="box-footer">&nbsp;</div>
</div>
