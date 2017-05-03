var xTplBlockButton = '<div class="-x-el-inline-button -x-el-block-edit -x-el-reset">' +
  '  <img src="' + _lang["url"] + '/assets/images/editor/icons/settings.svg" class="-x-el-icon"' +
  '    onMouseOver="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/settings-hover.svg\';"' +
  '    onMouseOut="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/settings.svg\';"' +
  '  >' +
  '  <ul class="-x-el-dropdown -x-el-reset">' +
  '    <li class="-x-el-block-background"><a href="javascript:void(0);">Background</a></li>' +
  '    <li class="separator"><hr></li>' +
  '    <li class="-x-el-block-move"><a href="javascript:void(0);">Move <div class="-x-el-caret"></div></a>' +
  '      <ul>' +
  '        <li class="-x-el-block-move-up"><a href="javascript:void(0);">Move up</a></li>' +
  '        <li class="-x-el-block-move-down"><a href="javascript:void(0);">Move down</a></li>' +
  '      </ul>' +
  '    </li>' +
  '    <li class="-x-el-block-insert"><a href="javascript:void(0);">Insert <div class="-x-el-caret"></div></a>' +
  '      <ul>' +
  '        <li class="-x-el-block-insert-above"><a href="javascript:void(0);">Above</a></li>' +
  '        <li class="-x-el-block-insert-below"><a href="javascript:void(0);">Below</a></li>' +
  '      </ul>' +
  '    </li>' +
  '    <li class="-x-el-block-edit-menu"><a href="javascript:void(0);">Edit <div class="-x-el-caret"></div></a>' +
  '      <ul>' +
  '        <li class="-x-el-block-edit-duplicate"><a href="javascript:void(0);">Duplicate</a></li>' +
  '        <li class="-x-el-block-edit-delete"><a href="javascript:void(0);">Delete</a></li>' +
  '      </ul>' +
  '    </li>' +
  '  </ul>' +
  '</div>';

var xTplListButton = '<div class="-x-el-inline-button -x-el-list-edit -x-el-reset">' + 
  ' <img src="' + _lang["url"] + '/assets/images/editor/icons/layers.svg" class="-x-el-icon"' +
  '   onMouseOver="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/layers-hover.svg\';"' +
  '   onMouseOut="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/layers.svg\';"' +
  ' >' +
  ' <ul class="-x-el-dropdown -x-el-reset">' +
  '   <li class="-x-el-list-edit"><a href="javascript:void(0);">Edit list...</a></li>' +
  ' </ul>' +
  '</div>';

var xTplImgButton = '<div class="-x-el-inline-button -x-el-img-edit -x-el-reset">' +
'  <img src="' + _lang["url"] + '/assets/images/editor/icons/image.svg" class="-x-el-icon"' +
'    onMouseOver="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/image-hover.svg\';"' +
'    onMouseOut="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/image.svg\';"' +
'  >' +
'  <ul class="-x-el-dropdown -x-el-reset">' +
'    <li class="-x-el-img-select"><a href="javascript:void(0);">Browse...</a></li>' +
'    <li class="-x-el-img-link"><a href="javascript:void(0);">Link...</a></li>' +
'    <li class="separator"><hr></li>' +
'    <li class="-x-el-img-hide"><a href="javascript:void(0);">Hide image</a></li>' +
'  </ul>' +
'</div>';

var xTplLinkButton = '<div class="-x-el-inline-button -x-el-list-edit -x-el-reset">' + 
  ' <img src="' + _lang["url"] + '/assets/images/editor/icons/link.svg" class="-x-el-icon"' +
  '   onMouseOver="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/link-hover.svg\';"' +
  '   onMouseOut="this.src = \'' + _lang["url"] + '/assets/images/editor/icons/link.svg\';"' +
  ' >' +
  ' <ul class="-x-el-dropdown -x-el-reset">' +
  '   <li class="-x-el-list-edit"><a href="javascript:void(0);">Edit link...</a></li>' +
  ' </ul>' +
  '</div>';