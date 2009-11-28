<!--[ $Id$ ]-->
<!--[include file="Eternizer_admin_menu.tpl"]-->
<h2><!--[gt text="Admin view"]--></h2>

<!--[eternizerundo assign="undo"]-->
<!--[if $undo ne '']-->
<a href="<!--[$undo]-->"><!--[gt text="Undo"]--></a>
<!--[/if]-->

<form class="z-form" action="<!--[pnmodurl modname=Eternizer type=admin func=adminViewRedirect]-->" method="post">
    <!--[foreach from=$list item=item]-->

    <div class="etz_entry z-clearfix <!--[cycle values='etz_bg1,etz_bg2']-->" >
        <div class="etz_author">
            <div class="etz_avatar">
                <strong><!--[$item.profile[$config.titlefield]]--></strong>
            </div>
            <dl class="etz_options">
                <dt>
                    <input type="checkbox" name="selected[<!--[$item.id]-->]" value="1" />
                </dt>
                <dd>
                    <!--[if $item.obj_status eq 'M']-->
                    <a href="<!--[pnmodurl modname=Eternizer type=admin func='changeStatus' status="A" id=$item.id goback=$goback]-->"><!--[pnimg modname='core' src='greenled.gif' set='icons/extrasmall' __title="Activate this entry" __alt="Activate this entry"]--></a>
                    <!--[else]-->
                    <a href="<!--[pnmodurl modname=Eternizer type=admin func='changeStatus' status="M" id=$item.id goback=$goback]-->"><!--[pnimg modname='core' src='redled.gif' set='icons/extrasmall' __title="Moderate this entry" __alt="Moderate this entry"]--></a>
                    <!--[/if]-->
                    <!--[if $item.rights.modify]-->
                    <a href="<!--[pnmodurl modname=Eternizer type=admin func='modify' id=$item.id goback=$goback]-->"><!--[pnimg modname='core' src='edit.gif' set='icons/extrasmall' __title="Edit this entry" __alt="Edit this entry"]--></a>
                    <!--[/if]-->
                    <!--[if $item.rights.delete]-->
                    <a href="<!--[pnmodurl modname=Eternizer type=admin func='suppress' id=$item.id goback=$goback]-->"><!--[pnimg modname='core' src='14_layer_deletelayer.gif' set='icons/extrasmall' __title="Edit this entry" __alt="Edit this entry"]--></a>
                    <!--[/if]-->
                </dd>
            </dl>
        </div>

        <div class="etz_body">
            <div class="etz_info">
                <strong class="etz_title"><!--[$item.cr_date|pndate_format:datetimebrief]--></strong>
            </div>
            <div class="etz_content">
                <!--[$item.text|pnvarprepfordisplay|nl2br|pnmodcallhooks]-->
            </div>
        </div>
    </div>
    
    <!--[foreachelse]-->
    <p class="z-informationmsg"><!--[gt text="No entries available."]--></p>
    <!--[/foreach]-->

    <fieldset>
        <legend><!--[gt text="Selected"]--></legend>
        <input type="submit" name="action[activate]" value="<!--[gt text="Activate"]-->" />
        <input type="submit" name="action[moderate]" value="<!--[gt text="Moderate"]-->" />
        <!--[if $rights.delete]-->
        <input type="submit" name="action[delete]" value="<!--[gt text="Delete"]-->" />
        <!--[/if]-->
    </fieldset>
</form>

<!--[include file="Eternizer_admin_footer.tpl"]-->