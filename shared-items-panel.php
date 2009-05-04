<?php if ($_POST['action'] == 'save') { ?><div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div><?php } ?>

<div class="wrap"><h2>Shared Items Post</h2>

<div class="gdsr">
<form method="post">
	<?php wp_nonce_field('shared-1' ); ?>
<input type="hidden" name="action" value="save" />
<table class="form-table"><tbody>
    <tr><th scope="row">Shared items url:</th>
        <td>
            <input type="text" name="share_url" id="adv_paypal_url" value="<?php echo $options["share_url"]; ?>" style="width: 720px" /><input type="hidden" name="share_id" id="adv_share_id" value="<?php echo $options["share_id"]; ?>" /> ID: <span id="filled_share_id"><?php echo $options["share_id"]; ?></span>
        </td>
    </tr>
    <tr><th scope="row">Refresh:</th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Period:</td>
                    <td align="left">
                    <select style="width: 180px;" name="refresh_period" id="refresh_period">
                        <option value="monthly"<?php echo $options["refresh_period"] == 'monthly' ? ' selected="selected"' : ''; ?>>Monthly</option>
                        <option value="weekly"<?php echo $options["refresh_period"] == 'weekly' ? ' selected="selected"' : ''; ?>>Weekly</option>
                        <option value="daily"<?php echo $options["refresh_period"] == 'daily' ? ' selected="selected"' : ''; ?>>Daily</option>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25">Time:</td>
                    <td align="left">
                        <input maxlength="8" type="text" name="refresh_time" id="refresh_time" value="<?php echo $options["refresh_time"]; ?>" style="width: 170px" /> [format: HH:MM or HH:MM AP (AM/PM)]
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><th scope="row">Post Template:</th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Title:</td>
                    <td><input type="text" name="post_title" id="post_title" value="<?php echo wp_specialchars(stripslashes($options["post_title"])); ?>" style="width: 570px" /><br />
                    List of allowed template elements:</td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"></td>
                    <td width="130"><strong>%DATE%</strong></td>
                    <td width="160"> : post publish date</td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Header:</td>
                    <td><input type="text" name="post_header_template" id="post_header_template" value="<?php echo wp_specialchars($options["post_header_template"]); ?>" style="width: 570px" /></td>
                </tr>
                <tr>
                    <td width="150" height="25">Footer:</td>
                    <td><input type="text" name="post_footer_template" id="post_footer_template" value="<?php echo wp_specialchars($options["post_footer_template"]); ?>" style="width: 570px" /></td>
                </tr>
            </table>
            <div class="gdsr-table-split"></div>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Item:</td>
                    <td><input type="text" name="post_item_template" id="post_item_template" value="<?php echo wp_specialchars($options["post_item_template"]); ?>" style="width: 570px" /><br />
                    List of allowed template elements:</td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"></td>
                    <td width="130"><strong>%TITLE%</strong></td>
                    <td width="160"> : feed item title</td>
                    <td width="10"></td>
                    <td width="130"><strong>%DATE%</strong></td>
                    <td width="160"> : item publish date</td>
                </tr>
                <tr>
                    <td width="150" height="25"></td>
                    <td width="130"><strong>%LINK%</strong></td>
                    <td width="160"> : link for the feed item</td>
                    <td width="10"></td>
                    <td width="130"><strong>%NOTE%</strong></td>
                    <td width="160"> : feed note</td>
                </tr>
            </table>
            <div class="gdsr-table-split"></div>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Note:</td>
                    <td><input type="text" name="post_note_template" id="post_note_template" value="<?php echo wp_specialchars($options["post_note_template"]); ?>" style="width: 570px" /><br />
                    List of allowed template elements:</td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25"></td>
                    <td width="130"><strong>%CONTENT%</strong></td>
                    <td width="160"> : note content</td>
                    <td width="10"></td>
                    <td width="130"><strong>%AUTHOR%</strong></td>
                    <td width="160"> : note author</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><th scope="row">Post Settings:</th>
        <td>
            <table cellpadding="0" cellspacing="0" class="previewtable">
                <tr>
                    <td width="150" height="25">Author:</td>
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
                    <td width="150" height="25">Category:</td>
                    <td>
                    <?php 
                        $dropdown_options = array('show_option_all' => '', 'hide_empty' => 0, 'hierarchical' => 1,
                            'show_count' => 0, 'depth' => 0, 'orderby' => 'ID', 'selected' => $options["post_category"], 'name' => 'post_category');
                        wp_dropdown_categories($dropdown_options);
                    ?>
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25">Tags:</td>
                    <td>
                    <input type="text" name="post_tags" id="post_tags" value="<?php echo $options["post_tags"]; ?>" style="width: 570px" />
                    </td>
                </tr>
                <tr>
                    <td width="150" height="25">Comments:</td>
                    <td>
                    <input type="checkbox" name="post_comments" id="post_comments"<?php if ($options["post_comments"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="post_comments">Allow posting of comments</label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</tbody></table>
<p class="submit"><input class="inputbutton" type="submit" value="Save Options" name="saving"/></p>
</form>
</div>
<div class="gdsr">
<table class="form-table"><tbody>
    <tr><th scope="row">Read feed:</th>
        <td>
            <form method="post">
            	<?php wp_nonce_field('shared-2' ); ?>
            <input type="hidden" name="action" value="runow" />
                <input class="inputbutton" type="submit" value="Run Now" name="saving" />
            </form>
        </td>
    </tr>
    <tr><th scope="row">Reset settings:</th>
        <td>
            <form method="post">
            	<?php wp_nonce_field('shared-3' ); ?>

            <input type="hidden" name="action" value="reset" />
                <input class="inputbutton" type="submit" value="Reset" name="saving" />
            </form>
        </td>
    </tr>
</table>
</div>
</div>
