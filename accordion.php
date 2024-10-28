<?php
/*
Plugin Name: Accordion
Plugin URI: http://spicyexpress.net
Description: A widget which displays the output of other plugins, using the <a href="http://bassistance.de/jquery-plugins/jquery-plugin-accordion/">Accordion</a> <a href="http://jquery.com">jQuery</a> plugin.The biggest advantage of this plugin is it loads your wordpress site faster than before, it saves huge space, provides tabbed browsing inside your sidebar.
Author: P Dayaparan
Version: 0.2
Author URI: mailto:daya@meyshan.com

*/

define('NUM_PLUGINS', 6);

function widget_accordion($args)
{
    extract($args);

	$here = get_option('siteurl') . "/wp-content/plugins/accordion";
	$options = get_option('widget_accordion');

	echo $before_widget;
	if($options['title']) echo $before_title . $options['title'] . $after_title;

	$list_tag = $options['list_tag'];
	$before_title = $options['before_title'];
	$after_title = $options['after_title'];

	echo '<div id="accordiondiv">';
	if($options['widget_type'] == 'accordion')
	{
        for($i = 1; $i <= NUM_PLUGINS; $i++)
        {
            if($options[$i]['function'])
            {
                $title = $options[$i]['title'];

                echo "{$before_title}{$title}{$after_title}
                <div><$list_tag>";

                eval($options[$i]['function']);

                echo "</$list_tag></div>";
            }
        }
    }
    else
    {
    	echo '<div class="accordion-tabwrap">';
    	for($i = 1; $i < NUM_PLUGINS; $i++)
    	{
    		if($options[$i]['function'])
    		{
    			$o = $options[$i];
    			echo "<div class=\"accordion-tab" . ($i == 1 ? 'on' : '') . "\" id=\"accordion-tab-$i\"><a href=\"#accordion-$i\" onclick=\"return accordion_tab($i);\">$o[title]</a></div>";
    		}
    	}
    	echo '</div>';
    	for($i = 1; $i < NUM_PLUGINS; $i++)
    	{
    		if($options[$i]['function'])
    		{
    			$o = $options[$i];
    			echo "<div " . ($i > 1 ? 'style="display: none" ' : '') . "id=\"accordion-$i\">\n<$list_tag>";
    			eval($options[$i]['function']);
    			echo "</$list_tag>\n</div>\n";
    		}
        }
    }
    echo '</div>';
        echo $after_widget;
}

function widget_accordion_control()
{
	$here = get_option('siteurl') . "/wp-content/plugins/accordion";

	$options = $newoptions = get_option('widget_accordion');

	if(!is_array($options)) $options = $newoptions = array();

	if($_POST["accordion-submit"])
	{
		$newoptions['widget_type'] = stripslashes($_POST["accordion-widget-type"]);
		$newoptions['title'] = stripslashes($_POST["accordion-title"]);
		$newoptions['before_title'] = stripslashes($_POST["accordion-before-title"]);
		$newoptions['after_title'] = stripslashes($_POST["accordion-after-title"]);
		$newoptions['list_tag'] = stripslashes($_POST["accordion-list-tag"]);
	    for($i = 1; $i <= NUM_PLUGINS; $i++)
	    {
			$newoptions[$i]['function'] = stripslashes($_POST["accordion-function-$i"]);
			$newoptions[$i]['title'] = stripslashes($_POST["accordion-title-$i"]);
		}
    }
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_accordion', $options);
	}

    $widget_type = attribute_escape($options['widget_type']);
	if(!$widget_type) $widget_type = 'tabs';
    $list_tag = attribute_escape($options['list_tag']);
	if(!$list_tag) $list_tag = 'ul';
	$before_title = attribute_escape($options['before_title']);
	if(!$before_title) $before_title = '&lt;h3&gt;';
	$after_title = attribute_escape($options['after_title']);
	if(!$after_title) $after_title = '&lt;/h3&gt;';
?>
      	<style type="text/css">
.helpTooltip
{
	border: 1px solid #448ABD;
	background-color: #CEF;
	padding: 10px;
	color: black;
}
.accordion-help
{
	vertical-align: bottom;
	position: relative;
	top: -2px;
}
.accordionconf-tab
{
	background: #def;
	border: 1px solid #448ABD;
	border-bottom: 0;
}
.seld
{
	background: white;
	position: relative;
	top: 1px;
}
#accordion-tabshelp
{
	display: none;
	border: 1px solid #448A8D;
	background: #def;
	padding: 10px;
	position: absolute;
	z-index: 1000;
	margin-top: 7em;
}
#accordion-tabshelp table
{
	background-color: white;
  	border-collapse: collapse;
}
#accordion-tabshelp table td
{
	padding: 2px 4px;
	border: 1px solid #448ABD;
}
        </style>
		<script language="javascript" type="text/javascript">
			function acctab(i)
			{
				for(n = 1; n <= <?php echo NUM_PLUGINS ?>; n++)
				{
					elem = document.getElementById("accordiontab" + n);
					if(i == n) elem.className = "accordionconf-tab seld";
					else elem.className = "accordionconf-tab";

					elem = document.getElementById("accordionconf" + n);
					if(i == n) elem.style.display="block";
					else elem.style.display="none";
				}

				return false;
			}
			function checkacc()
			{
				elem = document.getElementById('accordion-widget-type');
				if(elem.selectedIndex == 1)
					jQuery(".acconly").hide();
                else jQuery(".acconly").show();
			}
        </script>
    <table width="100%">
        <tr><td><label for="accordion-title">Title: </label></td>
        <td align="right"><input style="width: 180px" type="text" id="accordion-title" name="accordion-title" value="<?php echo $options['title'] ?>" /> <img title="The title for the overall widget. It is optional." class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" /></td></tr>

		<tr>
			<td><label for="accordion-widgget-type">Widget type </label></td>
			<td align="right">
				<select style="width: 188px" id="accordion-widget-type" name="accordion-widget-type" onchange="checkacc()">
				<option value="accordion" <?php if($widget_type=='accordion') echo 'selected="selected"'; ?>>jQuery Accordion</option>
				<option value="tabs" <?php if($widget_type=='tabs') echo 'selected="selected"'; ?>>Tabs</option>
				</select>
				 <img title="The way to display the content: using an &quot;Accordion&quot; or &quot;Tabs&quot;, which are simpler." class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" />
            </td>
        </tr>

		<tr class="acconly"><td><label for="accordion-before-title">Before title </label></td>
			<td align="right"><input style="width: 180px" id="accordion-before-title" name="accordion-before-title" type="text" value="<?php echo $before_title; ?>" /> <img title="Applies to Accordion mode only. Allows you to edit the HTML tags inserted before the title of each plugin. (the Accordion headers you click on). For example &amp;lt;h3 style=&quot;background: blue; padding: 10px&quot;&amp;gt;. The default is &amp;lt;h3&amp;gt;" class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" /></td></tr>

        <tr class="acconly"><td><label for="accordion-after-title">After title </label></td>
			<td align="right"><input style="width: 180px" id="accordion-after-title" name="accordion-after-title" type="text" value="<?php echo $after_title; ?>" /> <img title="The closing tag. Default is &amp;lt;/h3&amp;gt;" class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" /></td></tr>

			<tr>
			<td><label for="accordion-list-tag">List type </label></td>
			<td align="right">
				<select style="width: 188px" id="accordion-list-tag" name="accordion-list-tag">
				<option value="ol" <?php if($list_tag=='ol') echo 'selected="selected"'; ?>>Ordered</option>
				<option value="ul" <?php if($list_tag=='ul') echo 'selected="selected"'; ?>>Unordered</option>
				</select>
				<img title="Allows you to select which sort of list to display: Ordered list &amp;lt;ol&amp;gt; or Unordered list &amp;lt;ul&amp;gt;. This is here to allow better theme integration where needed. Default is Unordered." class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" />
            </td>
            </tr>
    </table>
    <script type="text/javascript">
		checkacc();
    </script>
    <br />
    Configure each plugin: <a href="#" onclick="jQuery('#accordion-tabshelp').toggle(); return false;"><img title="Edit the function and title for each of the plugins.  Note that the function should generate &amp;lt;li&amp;gt; list items. Click the help icon for some examples." class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" /></a>
    <div id="accordion-tabshelp">
		Some examples are:
		<table>
		<tr><td><b>Title</b></td><td><b>Function</b></td><td><b>Plugin</b> <img title="The plugin to use. It must be installed and enabled. The builtin functions need no plugin, they work automatically." class="accordion-help" src="<?php echo $here ?>/tip.gif" alt="Tip" /></a></td></tr>
		<tr><td>Categories</td><td>wp_list_cats();</td><td>(builtin function)</td></tr>
		<tr><td>Archives</td><td>wp_get_archives("type=monthly&amp;show_post_count=10");</td><td>(builtin function)</td></tr>
		<tr><td>Popular posts</td><td>akpc_most_popular();</td><td><a href="http://alexking.org/projects/wordpress">Popularity Contest</a></td></tr>
		<tr><td>Recent comments</td><td>get_recent_comments();</td><td><a href="http://blog.jodies.de/archiv/2004/11/13/recent-comments/">Get Recent Comments</a></td></tr>
		<tr><td>Recent posts</td><td>c2c_get_recent_posts(10);</td><td><a href="http://www.coffee2code.com/archives/2004/08/27/plugin-customizable-post-listings/">Customizable Post Listings</a></td></tr>
		</table>
    </div>
    <br />
<?php
	for($i = 1; $i <= NUM_PLUGINS; $i++) echo "<a id=\"accordiontab" . $i . "\" href=\"#\" onclick=\"return acctab($i)\" class=\"accordionconf-tab" . ($i == 1 ? ' seld' : '') . "\">Plugin $i</a>\n";
	echo '<div style="border: 1px solid #448ABD;">';

	for($i = 1; $i <= NUM_PLUGINS; $i++)
	{
		$title = attribute_escape($options[$i]['title']);
		$function = attribute_escape($options[$i]['function']);
?>
		<div id="accordionconf<?php echo $i ?>" style="display: <?php echo $i > 1 ? 'none': 'block' ?>" >
		<table>

			<tr><td><label for="accordion-function-<?php echo $i; ?>">Function </label></td>
			<td><input id="accordion-function-<?php echo $i; ?>" name="accordion-function-<?php echo $i; ?>" value="<?php echo $function; ?>" /></td></tr>

			<tr><td><label for="accordion-title-<?php echo $i; ?>">Title </label></td>
			<td><input id="accordion-title-<?php echo $i; ?>" name="accordion-title-<?php echo $i; ?>" type="text" value="<?php echo $title; ?>" /></td></tr>

        </table>
        </div>
<?php
	}
    echo '</div>
    <input type="hidden" name="accordion-submit" value="1" />
    <script type="text/javascript">
    	jQuery(".accordion-help").ToolTip({ className: "helpTooltip", position: "right" });
	</script>';
}

function widget_accordion_init()
{
	add_option('widget_accordion', $options);

	$options = get_option('widget_accordion');

	if(!function_exists('register_sidebar_widget')) return;
	widget_accordion_register();
}

function accordion_head()
{
	$options = get_option('widget_accordion');
	$here = get_option('siteurl') . "/wp-content/plugins/accordion";
if($options['widget_type'] == 'accordion')
{
	echo '<link rel="stylesheet" href="'.$here.'/accordion.css" type="text/css" media="screen, projection" />
	<script language="javascript" type="text/javascript" src="'.$here.'/jquery.js"></script>';
	echo '<script language="javascript" type="text/javascript" src="'.$here.'/jquery.accordion.pack.js"></script>
<script>
$(function()
{
	jQuery("#accordiondiv").Accordion({
		active: false,
		alwaysOpen: false,
		autoheight: true
	});
}) </script>';
}
else
	echo '<link rel="stylesheet" href="'.$here.'/jquery.tabs.css" type="text/css" media="print, projection, screen">
        <!-- Additional IE/Win specific style sheet (Conditional Comments) -->
        <!--[if lte IE 7]>
        <link rel="stylesheet" href="'.$here.'/jquery.tabs-ie.css" type="text/css" media="projection, screen">
        <![endif]-->
        <script type="text/javascript" language="javascript">
function accordion_tab(i)
{
	var n;
	for(n = 1; n <= ' . NUM_PLUGINS . '; n++)
	{
		el = document.getElementById("accordion-" + n);
		tab = document.getElementById("accordion-tab-" + n);
		if(!el) continue;
		if(n == i)
		{
			el.style.display = "block";
			tab.className = "accordion-tabon";
        }
		else
		{
			el.style.display = "none";
			tab.className = "accordion-tab";
		}
	}

	return false;
}
        </script>';
}

add_action('plugins_loaded', 'widget_accordion_init');
add_action('wp_head', 'accordion_head');

function widget_accordion_register() {
	register_sidebar_widget("Accordion", 'widget_accordion');
	register_widget_control("Accordion", 'widget_accordion_control', 350, 340);
}

?>