// cidr2regex 0.0.3
// 2009 xenowire
function cidr2regex( cidr )
{
	var cidrRegExp = new RegExp('([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\/([0-9]{1,2})');
	if( !cidr.match( cidrRegExp ) )
		return 'invalid arg.';

	var map = new Array();
	// 255
	map['0'] = new Array();
	map['0']['0']   = '[0-9]{1,3}';
	// 128
	map['1'] = new Array();
	map['1']['0']   = '([0-9]{0,1}[0-9]|1[0-1][0-9]|12[0-7])';
	map['1']['128'] = '(12[8-9]|1[3-9][0-9]|2[0-5][0-9])';
	// 64
	map['2'] = new Array();
	map['2']['0']   = '([0-5]{0,1}[0-9]|6[0-3])';
	map['2']['64']  = '(6[4-9]|[7-9][0-9]|1[0-1][0-9]|12[0-7])';
	map['2']['128'] = '(12[8-9]|1[3-8][0-9]|19[0-1])';
	map['2']['192'] = '(19[2-9]|2[0-5][0-9])';
	// 32
	map['3'] = new Array();
	map['3']['0']   = '([0-2]{0,1}[0-9]|3[0-1])';
	map['3']['32']  = '(3[2-9]|[4-5][0-9]|6[0-3])';
	map['3']['64']  = '(6[4-9]|[7-8][0-9]|9[0-5])';
	map['3']['96']  = '(9[6-9]|1[0-1][0-9]|12[0-7])';
	map['3']['128'] = '(12[8-9]|1[3-5][0-9])';
	map['3']['160'] = '(1[6-8][0-9]|19[0-1])';
	map['3']['192'] = '(19[2-9]|2[0-1][0-9]|22[0-3])';
	map['3']['224'] = '(22[4-9]|2[3-5][0-9])';
	// 16
	map['4'] = new Array();
	map['4']['0']   = '([0-9]|1[0-5])';
	map['4']['16']  = '(1[6-9]|2[0-9]|3[0-1])';
	map['4']['32']  = '(3[2-9]|4[0-7])';
	map['4']['48']  = '(4[8-9]|5[0-9]|6[0-3])';
	map['4']['64']  = '(6[4-9]|7[0-9])';
	map['4']['80']  = '(8[0-9]|9[0-5])';	
	map['4']['96']  = '(9[6-9]|10[0-9]|11[0-1])';
	map['4']['112'] = '(11[2-9]|12[0-7])';
	map['4']['128'] = '(12[8-9]|13[0-9]|14[0-3])';
	map['4']['144'] = '(14[4-9]|15[0-9])';
	map['4']['160'] = '(16[0-9]|17[0-5])';
	map['4']['176'] = '(17[6-9]|18[0-9]|19[0-1])';
	map['4']['192'] = '(19[2-9]|20[0-7])';
	map['4']['208'] = '(20[8-9]|21[0-9]|22[0-3])';
	map['4']['224'] = '(22[4-9]|23[0-9])';
	map['4']['240'] = '2[4-5][0-9]';
	// 8
	map['5'] = new Array();
	map['5']['0']   = '[0-7]';
	map['5']['8']   = '([8-9]|1[0-5])';
	map['5']['16']  = '(1[6-9]|2[0-3])';
	map['5']['24']  = '(2[4-9]|3[0-1])';
	map['5']['32']  = '3[2-9]';
	map['5']['40']  = '4[0-7]';
	map['5']['48']  = '(4[8-9]|5[0-5])';
	map['5']['56']  = '(5[6-9]|6[0-3])';
	map['5']['64']  = '(6[4-9]|7[0-1])';
	map['5']['72']  = '7[2-9]';
	map['5']['80']  = '8[0-7]';
	map['5']['88']  = '(8[8-9]|9[0-5])';
	map['5']['96']  = '(9[6-9]|10[0-3])';
	map['5']['104'] = '(10[4-9]|11[0-1])';
	map['5']['112'] = '11[2-9]';
	map['5']['120'] = '12[0-7]';
	map['5']['128']  = '(12[8-9]|13[0-5])';
	map['5']['136']  = '(13[6-9]|14[0-3])';
	map['5']['144']  = '(14[4-9]|15[0-1])';
	map['5']['152']  = '15[2-9]';
	map['5']['160']  = '16[0-7]';
	map['5']['168']  = '(16[8-9]|17[0-5])';
	map['5']['176']  = '(17[6-9]|18[0-3])';
	map['5']['184']  = '(18[4-9]|19[0-1])';
	map['5']['192']  = '19[2-9]';
	map['5']['200']  = '20[0-7]';
	map['5']['208']  = '(20[8-9]|21[0-5])';
	map['5']['216']  = '(21[6-9]|22[0-3])';
	map['5']['224']  = '(22[4-9]|23[0-1])';
	map['5']['232']  = '23[2-9]';
	map['5']['240']  = '24[0-7]';
	map['5']['248']  = '(24[8-9]|25[0-5])';
	// 4
	map['6'] = new Array();
	map['6']['0']   = '[0-3]';
	map['6']['4']   = '[4-7]';
	map['6']['8']   = '([8-9]|1[0-1])';
	map['6']['12']  = '1[2-5]';
	map['6']['16']  = '1[6-9]';
	map['6']['20']  = '2[0-3]';
	map['6']['24']  = '2[4-7]';
	map['6']['28']  = '(2[8-9]|3[0-1])';
	map['6']['32']  = '3[2-5]';
	map['6']['36']  = '3[6-9]';
	map['6']['40']  = '4[0-3]';
	map['6']['44']  = '4[4-7]';
	map['6']['48']  = '(4[8-9]|5[0-1])';
	map['6']['52']  = '5[2-5]';
	map['6']['56']  = '5[6-9]';
	map['6']['60']  = '6[0-3]';
	map['6']['64']  = '6[4-7]';
	map['6']['68']  = '(6[8-9]|7[0-1])';
	map['6']['72']  = '7[2-5]';
	map['6']['76']  = '7[6-9]';
	map['6']['80']  = '8[0-3]';
	map['6']['84']  = '8[4-7]';
	map['6']['88']  = '(8[8-9]|9[0-1])';
	map['6']['92']  = '9[2-5]';
	map['6']['96']  = '9[6-9]';
	map['6']['100'] = '10[0-3]';
	map['6']['104'] = '10[4-7]';
	map['6']['108'] = '(10[8-9]|11[0-1])';
	map['6']['112'] = '11[2-5]';
	map['6']['116'] = '11[6-9]';
	map['6']['120'] = '12[0-3]';
	map['6']['124'] = '12[4-7]';
	map['6']['128'] = '(12[8-9]|13[0-1])';
	map['6']['132'] = '13[2-5]';
	map['6']['136'] = '13[6-9]';
	map['6']['140'] = '14[0-3]';
	map['6']['144'] = '14[4-7]';
	map['6']['148'] = '(14[8-9]|15[0-1])';
	map['6']['152'] = '15[2-5]';
	map['6']['156'] = '15[6-9]';
	map['6']['160'] = '16[0-3]';
	map['6']['164'] = '16[4-7]';
	map['6']['168'] = '(16[8-9]|17[0-1])';
	map['6']['172'] = '17[2-5]';
	map['6']['176'] = '17[6-9]';
	map['6']['180'] = '18[0-3]';
	map['6']['184'] = '18[4-7]';
	map['6']['188'] = '(18[8-9]|19[0-1])';
	map['6']['192'] = '19[2-5]';
	map['6']['196'] = '19[6-9]';
	map['6']['200'] = '20[0-3]';
	map['6']['204'] = '20[4-7]';
	map['6']['208'] = '(20[8-9]|21[0-1])';
	map['6']['212'] = '21[2-5]';
	map['6']['216'] = '21[6-9]';
	map['6']['220'] = '22[0-3]';
	map['6']['224'] = '22[4-7]';
	map['6']['228'] = '(22[8-9]|23[0-1])';
	map['6']['232'] = '23[2-5]';
	map['6']['236'] = '23[6-9]';
	map['6']['240'] = '24[0-3]';
	map['6']['244'] = '24[4-7]';
	map['6']['248'] = '(24[8-9]|25[0-1])';
	map['6']['252'] = '25[2-5]';

	var bip = 0;
	switch( RegExp.$5 )
	{
	case '0':
		return map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '1':
		bip = parseInt( parseInt(RegExp.$1) / 128 ) * 128;
		return map[1][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '2':
		bip = parseInt( parseInt(RegExp.$1) / 64 ) * 64;
		return map[2][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '3':
		bip = parseInt( parseInt(RegExp.$1) / 32 ) * 32;
		return map[3][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '4':
		bip = parseInt( parseInt(RegExp.$1) / 16 ) * 16;
		return map[4][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '5':
		bip = parseInt( parseInt(RegExp.$1) / 8 ) * 8;
		return map[5][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '6':
		bip = parseInt( parseInt(RegExp.$1) / 4 ) * 4;
		return map[6][bip] + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '7':
		bip = parseInt( parseInt(RegExp.$1) / 2 ) * 2;
		switch( bip.toString().length )
		{
		case 2:
			return bip.toString().substr(0,1) + '[' + bip.toString().charAt(1) + '-' + (bip+1).toString().charAt(1) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
		case 3:
			return bip.toString().substr(0,2) + '[' + bip.toString().charAt(2) + '-' + (bip+1).toString().charAt(2) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
		default:
			return '[' + bip + '-' + (bip+1) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
		}

	case '8':
		return RegExp.$1 + '\\.' + map[0][0] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '9':
		bip = parseInt( parseInt(RegExp.$2) / 128 ) * 128;
		return RegExp.$1 + '\\.' + map[1][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '10':
		bip = parseInt( parseInt(RegExp.$2) / 64 ) * 64;
		return RegExp.$1 + '\\.' + map[2][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '11':
		bip = parseInt( parseInt(RegExp.$2) / 32 ) * 32;
		return RegExp.$1 + '\\.' + map[3][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '12':
		bip = parseInt( parseInt(RegExp.$2) / 16 ) * 16;
		return RegExp.$1 + '\\.' + map[4][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '13':
		bip = parseInt( parseInt(RegExp.$2) / 8 ) * 8;
		return RegExp.$1 + '\\.' + map[5][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '14':
		bip = parseInt( parseInt(RegExp.$2) / 4 ) * 4;
		return RegExp.$1 + '\\.' + map[6][bip] + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '15':
		bip = parseInt( parseInt(RegExp.$2) / 2 ) * 2;
		switch( bip.toString().length )
		{
		case 2:
			return RegExp.$1 + '\\.' + bip.toString().substr(0,1) + '[' + bip.toString().charAt(1) + '-' + (bip+1).toString().charAt(1) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0];
		case 3:
			return RegExp.$1 + '\\.' + bip.toString().substr(0,2) + '[' + bip.toString().charAt(2) + '-' + (bip+1).toString().charAt(2) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0];
		default:
			return RegExp.$1 + '\\.' + '[' + bip + '-' + (bip+1) + ']' + '\\.' + map[0][0] + '\\.' + map[0][0];
		}

	case '16':
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[0][0] + '\\.' + map[0][0];
	case '17':
		bip = parseInt( parseInt(RegExp.$3) / 128 ) * 128;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[1][bip] + '\\.' + map[0][0];
	case '18':
		bip = parseInt( parseInt(RegExp.$3) / 64 ) * 64;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[2][bip] + '\\.' + map[0][0];
	case '19':
		bip = parseInt( parseInt(RegExp.$3) / 32 ) * 32;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[3][bip] + '\\.' + map[0][0];
	case '20':
		bip = parseInt( parseInt(RegExp.$3) / 16 ) * 16;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[4][bip] + '\\.' + map[0][0];
	case '21':
		bip = parseInt( parseInt(RegExp.$3) / 8 ) * 8;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[5][bip] + '\\.' + map[0][0];
	case '22':
		bip = parseInt( parseInt(RegExp.$3) / 4 ) * 4;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + map[6][bip] + '\\.' + map[0][0];
	case '23':
		bip = parseInt( parseInt(RegExp.$3) / 2 ) * 2;
		switch( bip.toString().length )
		{
		case 2:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + bip.toString().substr(0,1) + '[' + bip.toString().charAt(1) + '-' + (bip+1).toString().charAt(1) + ']' + '\\.' + map[0][0];
		case 3:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + bip.toString().substr(0,2) + '[' + bip.toString().charAt(2) + '-' + (bip+1).toString().charAt(2) + ']' + '\\.' + map[0][0];
		default:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + '[' + bip + '-' + (bip+1) + ']' + '\\.' + map[0][0];
		}

	case '24':
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[0][0];
	case '25':
		bip = parseInt( parseInt(RegExp.$4) / 128 ) * 128;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[1][bip];
	case '26':
		bip = parseInt( parseInt(RegExp.$4) / 64 ) * 64;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[2][bip];
	case '27':
		bip = parseInt( parseInt(RegExp.$4) / 32 ) * 32;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[3][bip];
	case '28':
		bip = parseInt( parseInt(RegExp.$4) / 16 ) * 16;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[4][bip];
	case '29':
		bip = parseInt( parseInt(RegExp.$4) / 8 ) * 8;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[5][bip];
	case '30':
		bip = parseInt( parseInt(RegExp.$4) / 4 ) * 4;
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + map[6][bip];
	case '31':
		bip = parseInt( parseInt(RegExp.$4) / 2 ) * 2;
		switch( bip.toString().length )
		{
		case 2:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + bip.toString().substr(0,1) + '[' + bip.toString().charAt(1) + '-' + (bip+1).toString().charAt(1) + ']';
		case 3:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + bip.toString().substr(0,2) + '[' + bip.toString().charAt(2) + '-' + (bip+1).toString().charAt(2) + ']';
		default:
			return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + '[' + bip + '-' + (bip+1) + ']';
		}

	case '32':
		return RegExp.$1 + '\\.' + RegExp.$2 + '\\.' + RegExp.$3 + '\\.' + RegExp.$4;
	}
	
	return 'invalid arg.';
}
