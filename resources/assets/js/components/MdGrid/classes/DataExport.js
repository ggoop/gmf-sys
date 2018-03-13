import Row from './Row';

import saveAs from './FileSaver';
import LoadScript from 'gmf/core/utils/LoadScript';
export default class DataExport {
  constructor(datas, columns) {
    if (datas && datas.length)
      this.rows = datas.map(rowData => new Row(rowData, columns));
    else
      this.rows = [];
    this.columns = columns;
  }
  toExcel(title) {
    LoadScript('/assets/vendor/gmf-sys/xlsx/xlsx.full.min.js').then(() => {
      title = title || 'Sheet1';
      var wb = {
        SheetNames: [],
        Sheets: {},
        Props: {}
      };
      var ws = this.sheet_from_array_of_arrays(this.rows, this.columns);

      /* add worksheet to workbook */
      wb.SheetNames.push(title);
      wb.Sheets[title] = ws;
      var wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'binary' });
      saveAs(new Blob([this.s2ab(wbout)], { type: "application/octet-stream" }), title + ".xlsx")
    })

  }
  s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
  }
  sheet_from_array_of_arrays(rows, columns) {
    var ws = {};
    var range = { s: { c: 10000000, r: 10000000 }, e: { c: 0, r: 0 } };
    //header
    for (var HR = 0; HR < 1; HR++) {
      _.forEach(columns, (column, C) => {
        if (range.s.r > HR) range.s.r = HR;
        if (range.s.c > C) range.s.c = C;
        if (range.e.r < HR) range.e.r = HR;
        if (range.e.c < C) range.e.c = C;

        var cell = { v: column.label || column.field, t: 's' };
        if (cell.v == null) return;
        cell.s = { fill: { bgColor: '#ff89dd' } };
        var cell_ref = XLSX.utils.encode_cell({ c: C, r: HR });
        ws[cell_ref] = cell;
      });
    }

    //body
    _.forEach(rows, (row, R) => {
      R += HR;
      _.forEach(columns, (column, C) => {
        if (range.s.r > R) range.s.r = R;
        if (range.s.c > C) range.s.c = C;
        if (range.e.r < R) range.e.r = R;
        if (range.e.c < C) range.e.c = C;


        var cell = { v: row.getValue(column.field) };
        if (cell.v == null) return;

        var cell_ref = XLSX.utils.encode_cell({ c: C, r: R });

        if (typeof cell.v === 'number') cell.t = 'n';
        else if (typeof cell.v === 'boolean') cell.t = 'b';
        else if (cell.v instanceof Date) {
          cell.t = 'n';
          cell.z = XLSX.SSF._table[14];
          cell.v = this.datenum(cell.v);
        } else cell.t = 's';
        ws[cell_ref] = cell;
      });
    });
    if (range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
    return ws;
  }
  datenum(v, date1904) {
    if (date1904) v += 1462;
    var epoch = Date.parse(v);
    return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
  }
}
