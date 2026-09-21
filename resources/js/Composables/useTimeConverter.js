export function minutesToTime(val) {
    if (val === null || val === undefined || val === '') return '';
    if (typeof val === 'string' && val.includes(':')) return val;
 
    let totalMinutos = Number(val);
    if (isNaN(totalMinutos)) return '';
    if (!Number.isInteger(totalMinutos)) {
        totalMinutos = Math.round(totalMinutos * 60);
    }
    const horas = Math.floor(totalMinutos / 60);
    const minutos = totalMinutos % 60;
    return `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}`;
}
 