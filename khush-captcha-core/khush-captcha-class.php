<?php
/*
License: GPL2

  Copyright 2013  khushapps.com : khushapps@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.q

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class khushCaptcha {

	public function checkKhushCaptcha($khush_captcha_gen) {
		$khush_captcha_gen_clean=str_replace(' ', '', $khush_captcha_gen);
		$khush_captcha_session = '';
		@$khush_captcha_session = $_SESSION['khush_captcha'];
		if ($khush_captcha_session == $khush_captcha_gen_clean) {
			return true;
		}
		return false;
	}
	
}
?>
