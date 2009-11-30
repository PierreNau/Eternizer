<!--[ $Id$ ]-->
<div class="etz_entry z-clearfix <!--[cycle values='etz_bg1,etz_bg2']-->" >
    <div class="etz_author">
        <div class="etz_avatar">
            <!--[assign var='defaultavatar' value="`$baseurl``$avatarpath`/blank.gif"]-->
            <!--[if $cr_uid > 1]-->
            <!--[pnusergetvar name=avatar uid=$cr_uid assign=avatar]-->
            <!--[if isset($avatar) AND !empty($avatar) AND $avatar neq 'blank.gif' AND $avatar neq 'gravatar.gif']-->
            <!--[assign var='defaultavatar' value="`$baseurl``$avatarpath`/`$avatar`"|default:$defaultavatar]-->
            <!--[/if]-->
            <!--[/if]-->
            <img src="<!--[eternizergravatar email=$profile.1 size=80 default=$defaultavatar]-->" alt="avatar" />
        </div>
        <dl class="etz_options">
            <!--[foreach from=$profile key=pid item=value]-->
            <!--[if $value != '' && $pid != $config.titlefield]-->
            <dt><strong><!--[$config.profile.$pid.title]--></strong></dt>
            <dd>
                <!--[if $config.profile.$pid.type eq 'mail']-->
                <a href="mailto:<!--[$value]-->"><!--[$value]--></a>
                <!--[elseif $config.profile.$pid.type eq 'url']-->
                <a href="<!--[$value]-->"><!--[$value]--></a>
                <!--[else]-->
                <!--[$value]-->
                <!--[/if]-->
            </dd>
            <!--[/if]-->
            <!--[/foreach]-->
            <!--[if $pncore.logged_in eq true]-->
            <dd>
                <!--[if $right_comment && !$right_edit]-->
                <a href="<!--[pnmodurl modname=Eternizer type=admin func=modify id=$id]-->" title="<!--[gt text="Comment this entry"]-->"><!--[pnimg modname=core src=comment.gif set='icons/extrasmall' __title="Comment this entry" __alt="Comment this entry"]--></a>
                <!--[/if]-->
                <!--[if $right_edit]-->
                <a href="<!--[pnmodurl modname=Eternizer type=admin func=modify id=$id]-->" title="<!--[gt text="Edit this entry"]-->"><!--[pnimg modname=core src=edit.gif set='icons/extrasmall' __title="Edit this entry" __alt="Edit this entry"]--></a>
                <!--[/if]-->
                <!--[if $right_delete]-->
                <a href="<!--[pnmodurl modname=Eternizer type=admin func=suppress id=$id]-->" title="<!--[gt text="Delete this entry"]-->"><!--[pnimg modname=core src=14_layer_deletelayer.gif set='icons/extrasmall' __title="Delete this entry" __alt="Delete this entry"]--></a>
                <!--[/if]-->
            </dd>
            <!--[/if]-->
        </dl>
    </div>

    <div class="etz_body">
        <div class="etz_info">
            <strong class="etz_title"><!--[$profile[$config.titlefield]|pnvarprepfordisplay]--> (<!--[$cr_date|pndate_format:datetimebrief]-->)</strong>
        </div>
        <div class="etz_content">
            <!--[$text|pnvarprepfordisplay|nl2br|pnmodcallhooks]-->
            <!--[if $comment]-->
            <p style="margin-top: 2em;" class="entry-comment"><strong class="entry-comment-label"><!--[gt text="Comment"]-->:</strong><br />
                <!--[$comment]-->
            </p>
            <!--[/if]-->
        </div>
    </div>
</div>
