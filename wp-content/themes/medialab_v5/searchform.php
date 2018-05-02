<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
	<div>
		<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="search here" />
		<input type="submit" id="searchsubmit" value="Chercher" />
	</div>
</form>
