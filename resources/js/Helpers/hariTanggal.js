import moment from 'moment'

export default function useHariTanggal() {
  //method "numberBomItemFormat"
  const hariTanggal = (value) => {
    
    // let dt = moment(value, "YYYY-MM-DD HH:mm:ss");

    // let hari = dt.format('dddd').substring(0,3);
    let hari = value.substring(0,3);

    let hari_ini = '';
 
	switch(hari){
		case 'Sun':
			hari_ini = "Minggu";
		break;
 
		case 'Mon':			
			hari_ini = "Senin";
		break;
 
		case 'Tue':
			hari_ini = "Selasa";
		break;
 
		case 'Wed':
			hari_ini = "Rabu";
		break;
 
		case 'Thu':
			hari_ini = "Kamis";
		break;
 
		case 'Fri':
			hari_ini = "Jumat";
		break;
 
		case 'Sat':
			hari_ini = "Sabtu";
		break;
		
		default:
			hari_ini = "Tidak di ketahui";		
		break;
	}
 
	return hari_ini;
  }

  return { hariTanggal };
}