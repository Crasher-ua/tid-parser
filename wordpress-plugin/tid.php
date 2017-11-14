<?php
/**
 * Plugin Name:  TrackItDown
 * Description:  Слив trackitdown.net, поиск новых превьюшек.
 * Version:      0.0.4
 * Author:       Crasher
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package    Track_It_Down
 * @since      0.0.4
 * @author     Crasher
 * @copyright  Copyright (c) 2015, Crasher
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */


add_action('wp_dashboard_setup',function(){
	wp_add_dashboard_widget(
		'track-it-down',
		'База trackitdown.net',
		'tid_dashboard_widget_function'
	);
});

function tid_dashboard_widget_function() {
	include(dirname(__FILE__) . '/includes/dashboard.php');
	/*echo "Тут будет жить trackitdown. Пока что не живёт, но будет. Держите пока непонятную картинку:<br/>
			<img src='/tid/stats/img?".rand()."' style='width:100%'/>";*/
}
