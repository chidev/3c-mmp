<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * 3rd Corner Studios Wordpress Framework
 *
 * @version			0.001
 * @package			TC_WP_001
 * @author			Jean-Patrick Smith
 * @copyright		2011 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */
	
	
	
	function tc_comment($comment, $args, $depth)
	{
		$GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>">
				<div class="comment-author vcard">
				<?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
					<div class="comment-meta commentmetadata">
						<div class="author-span"><?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?></div>
						<div class="comment-date"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="comment-content">
					<?php if ($comment->comment_approved == '0') : ?>
					<div class="spacer5"></div>
					<em><?php _e('Your comment is awaiting moderation.') ?></em>
					<br />
					<?php endif; ?>
					
					<div class="spacer5"></div>
					
					<?php comment_text() ?>
					
					<div class="reply">
						<?php comment_reply_link(array_merge( $args, array('reply_text' => 'Reply', 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
					</div>
				</div>
			</div>
		</li>
	<?php
	}
	
	
	
?>