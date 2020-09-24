import './js/texyla.js';
import './js/selection.js';
import './js/texy.js';
import './js/buttons.js';
import './js/dom.js';
import './js/view.js';
import './js/ajaxupload.js';
import './js/window.js';
import './languages/cs.js';
import './languages/sk.js';
import './languages/en.js';
import './plugins/keys/keys.js';
import './plugins/resizableTextarea/resizableTextarea.js';
import './plugins/img/img.js';
import './plugins/table/table.js';
import './plugins/link/link.js';
import './plugins/emoticon/emoticon.js';
import './plugins/symbol/symbol.js';
import './plugins/files/files.js';
import './plugins/color/color.js';
import './plugins/textTransform/textTransform.js';
import './plugins/youtube/youtube.js';
import './plugins/gravatar/gravatar.js';

$(function () {
	$(".texyla").texyla({
		toolbar: [
			'h1', 'h2', 'h3', 'h4',
			null,
			'bold', 'italic',
			null,
			'center', ['left', 'right', 'justify'],
			null,
			'ul', 'ol', ["olAlphabetSmall", "olAlphabetBig", "olRomans", "olRomansSmall"],
			null,
			{ type: "label", text: "Vlož"}, 'link', 'img', 'table', 'emoticon', 'symbol',
			null,
			'color', 'textTransform',
			null,
			'files', 'youtube', 'gravatar',
			null,
			'div', ['html', 'blockquote', 'text', 'comment'],
			null,
			'code',	['codeHtml', 'codeCss', 'codeJs', 'codePhp', 'codeSql'], 'codeInline',
			null,
			/*{ type: "label", text: "Ostatné"}, [*/'sup', 'sub', 'del', 'acronym', 'hr', 'notexy'/*, 'web']*/

		],
		texyCfg: "admin",
		bottomLeftToolbar: ['edit', 'preview', 'htmlPreview'],
    bottomRightPreviewToolbar: [],
		buttonType: "span",
		tabs: true,
    language: "sk"
	});

	$.texyla({
		buttonType: "button"
	});
});