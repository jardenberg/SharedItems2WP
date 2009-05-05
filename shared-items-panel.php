<?php if ($_POST['action'] == 'save') { ?><div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div><?php } ?>

<div class="wrap"><h2>Shared Items Post</h2>

<div class="gdsr">
<form method="post">
	<?php wp_nonce_field('shared-1' ); ?>
<input type="hidden" name="action" value="save" />
<table class="form-table"><tbody>
    <tr><th scope="row"><label for="adv_paypal_url">Shared items url:</label></th>
        <td>
            <input type="text" name="share_url" id="adv_paypal_url" value="<?php echo $options["share_url"]; ?>" style="width: 720px" /><input type="hidden" name="share_id" id="adv_share_id" value="<?php echo $options["share_id"]; ?>" /> ID: <span id="filled_share_id"><?php if ( !empty ( $options["share_id"] ) ): echo $options["share_id"]; else: echo 'Please insert shared items URL'; endif; ?></span>
        </td>
    </tr>
    <tr><th scope="row"><label for="refresh_period">Refresh:</label></th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="refresh_period">Period:</label></td>
                    <td align="left">
                    <select style="width: 180px;" name="refresh_period" id="refresh_period">
                        <option value="monthly"<?php echo $options["refresh_period"] == 'monthly' ? ' selected="selected"' : ''; ?>>Monthly</option>
                        <option value="weekly"<?php echo $options["refresh_period"] == 'weekly' ? ' selected="selected"' : ''; ?>>Weekly</option>
                        <option value="daily"<?php echo $options["refresh_period"] == 'daily' ? ' selected="selected"' : ''; ?>>Daily</option>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25"><label for="refresh_time">Time:</label></td>
                    <td align="left">
                        <input maxlength="8" type="text" name="refresh_time" id="refresh_time" value="<?php echo $options["refresh_time"]; ?>" style="width: 170px" /> [format: HH:MM or HH:MM AP (AM/PM)]
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><th scope="row"><label for="post_title">Post Template:</label></th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="post_title">Title:</label></td>
                    <td><input type="text" name="post_title" id="post_title" value="<?php echo wp_specialchars(stripslashes($options["post_title"])); ?>" style="width: 570px" /><br />
                    List of available template elements:</td>
                </tr>
            </table>
			<div class="column">
				<?php foreach ( $options['title_elements'] as $tag => $description ): ?>
					<div><code><?=$tag?></code> <span><?=$description?></span></div>
				<?php endforeach; ?>
			</div>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="post_header_template">Header:</label></td>
                    <td><input type="text" name="post_header_template" id="post_header_template" value="<?php echo wp_specialchars($options["post_header_template"]); ?>" style="width: 570px" /></td>
                </tr>
                <tr>
                    <td width="150" height="25"><label for="post_footer_template">Footer:</label></td>
                    <td><input type="text" name="post_footer_template" id="post_footer_template" value="<?php echo wp_specialchars($options["post_footer_template"]); ?>" style="width: 570px" /></td>
                </tr>
            </table>
            <div class="gdsr-table-split"></div>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="post_item_template">Item:</label></td>
                    <td><input type="text" name="post_item_template" id="post_item_template" value="<?php echo wp_specialchars($options["post_item_template"]); ?>" style="width: 570px" /><br />
                    List of available template elements:</td>
                </tr>
            </table>
			<div class="column">
				<?php foreach ( $options['item_elements'] as $tag => $description ): ?>
					<div><code><?=$tag?></code> <span><?=$description?></span></div>
				<?php endforeach; ?>
			</div>
            <div class="gdsr-table-split"></div>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="post_note_template">Note:</label></td>
                    <td><input type="text" name="post_note_template" id="post_note_template" value="<?php echo wp_specialchars($options["post_note_template"]); ?>" style="width: 570px" /><br />
                    List of available template elements:</td>
                </tr>
            </table>
            <div class="column">
				<?php foreach ( $options['annotation_elements'] as $tag => $description ): ?>
					<div><code><?=$tag?></code> <span><?=$description?></span></div>
				<?php endforeach; ?>
			</div>
        </td>
    </tr>
    <tr><th scope="row"><label for="post_author">Post Settings:</label></th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"><label for="post_author">Author:</label></td>
                    <td>
                    <select name="post_author" id="post_author">
                    <option selected="selected"
                    <?php
                        $all_users = get_users_of_blog();
                        foreach ($all_users as $u) {
                            $selected = "";
                            if ($u->user_id == $options["post_author"]) $selected = ' selected="selected"';
                            echo '<option value="'.$u->user_id.'"'.$selected.'>'.$u->display_name.'</option>';
                        }
                    ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25"><label for="post_category">Category:</label></td>
                    <td>
                    <?php 
                        $dropdown_options = array('show_option_all' => '', 'hide_empty' => 0, 'hierarchical' => 1,
                            'show_count' => 0, 'depth' => 0, 'orderby' => 'ID', 'selected' => $options["post_category"], 'name' => 'post_category');
                        wp_dropdown_categories($dropdown_options);
                    ?>
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25"><label for="post_tags">Tags:</label></td>
                    <td>
                    <input type="text" name="post_tags" id="post_tags" value="<?php echo $options["post_tags"]; ?>" style="width: 570px" />
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25"><label for="post_comments">Comments:</label></td>
                    <td>
                    <input type="checkbox" name="post_comments" id="post_comments"<?php if ($options["post_comments"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="post_comments">Allow posting of comments</label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</tbody></table>
<p class="submit"><input type="submit" value="Save Options" name="saving"/></p>
</form>
</div>
<div class="gdsr submit">

	<h2>Misc. operations</h2>
	
		<form method="post" class="friendlyform">
			<?php wp_nonce_field('shared-2' ); ?>
			<input type="hidden" name="action" value="runow" />
			<input type="submit" value="Run Now" name="saving" />
		</form>
		
		<form method="post">
			<?php wp_nonce_field('shared-3' ); ?>
			<input type="hidden" name="action" value="reset" />
			<input type="submit" value="Reset" name="saving" />
		</form>
	
</div>
</div>
