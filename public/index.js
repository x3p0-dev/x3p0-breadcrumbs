(()=>{var e,t={52:(e,t,r)=>{"use strict";const a=window.wp.blocks,l=JSON.parse('{"apiVersion":3,"version":"20241006","name":"x3p0/breadcrumbs","title":"X3P0: Breadcrumbs","category":"widgets","description":"Add a breadcrumb trail back to the site homepage. Breadcrumb items appear as placeholders in the editor and will populate with the correct data on the site front end.","keywords":["breadcrumb","trail","navigation"],"attributes":{"justifyContent":{"type":"string","default":""},"showHomeLabel":{"type":"boolean","default":true},"showOnHomepage":{"type":"boolean","default":false},"showTrailEnd":{"type":"boolean","default":true},"homePrefix":{"type":"string","default":""},"homePrefixType":{"type":"string","default":""},"markup":{"type":"string","default":"microdata"},"separator":{"type":"string","default":"chevron"},"separatorType":{"type":"string","default":"mask"}},"supports":{"anchor":true,"align":true,"html":false,"__experimentalBorder":{"radius":true,"color":true,"width":true,"style":true,"__experimentalDefaultControls":{"width":true,"color":true}},"color":{"link":true,"gradients":true,"__experimentalDefaultControls":{"background":true,"text":true,"link":true}},"spacing":{"margin":true,"padding":true,"__experimentalDefaultControls":{"padding":true}},"typography":{"fontSize":true,"lineHeight":true,"__experimentalFontStyle":true,"__experimentalFontWeight":true,"__experimentalFontFamily":true,"__experimentalLetterSpacing":true,"__experimentalTextTransform":true,"__experimentalDefaultControls":{"fontSize":true}}},"textdomain":"x3p0-breadcrumbs","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css","example":{}}'),n=window.wp.element,o=(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("g",null,(0,n.createElement)("rect",{fill:"none",height:"24",width:"24"})),(0,n.createElement)("g",null,(0,n.createElement)("polygon",{points:"15.5,5 11,5 16,12 11,19 15.5,19 20.5,12"}),(0,n.createElement)("polygon",{points:"8.5,5 0,5 0,12 0,19 8.5,19 13.5,12"}))),s=window.wp.i18n,i=window.wp.hooks,p=[{value:"",label:(0,s.__)("None","x3p0-breadcrumbs"),type:"",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0z",fill:"none"}),(0,n.createElement)("path",{d:"M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zM4 12c0-4.4 3.6-8 8-8 1.8 0 3.5.6 4.9 1.7L5.7 16.9C4.6 15.5 4 13.8 4 12zm8 8c-1.8 0-3.5-.6-4.9-1.7L18.3 7.1C19.4 8.5 20 10.2 20 12c0 4.4-3.6 8-8 8z"}))},{value:"outline",label:(0,s.__)("Home: Outlined","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0V0z",fill:"none"}),(0,n.createElement)("path",{d:"M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"}))},{value:"fill",label:(0,s.__)("Home: Filled","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0z",fill:"none"}),(0,n.createElement)("path",{d:"M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"}))},{value:"house-outline",label:(0,s.__)("House: Outlined","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("g",null,(0,n.createElement)("rect",{fill:"none",height:"24",width:"24"})),(0,n.createElement)("g",null,(0,n.createElement)("g",null,(0,n.createElement)("path",{d:"M19,9.3V4h-3v2.6L12,3L2,12h3v8h6v-6h2v6h6v-8h3L19,9.3z M17,18h-2v-6H9v6H7v-7.81l5-4.5l5,4.5V18z"}),(0,n.createElement)("path",{d:"M10,10h4c0-1.1-0.9-2-2-2S10,8.9,10,10z"}))))},{value:"house-fill",label:(0,s.__)("House: Filled","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("g",null,(0,n.createElement)("rect",{fill:"none",height:"24",width:"24"})),(0,n.createElement)("g",null,(0,n.createElement)("path",{d:"M19,9.3V4h-3v2.6L12,3L2,12h3v8h5v-6h4v6h5v-8h3L19,9.3z M10,10c0-1.1,0.9-2,2-2s2,0.9,2,2H10z"})))},{value:"🏠",label:(0,s.__)("Emoji: House","x3p0-breadcrumbs"),type:"text",icon:"🏠"},{value:"🏡",label:(0,s.__)("Emoji: House Garden","x3p0-breadcrumbs"),type:"text",icon:"🏡"},{value:"🏘",label:(0,s.__)("Emoji: Houses","x3p0-breadcrumbs"),type:"text",icon:"🏘"}],c=[{value:"chevron",label:(0,s.__)("Chevron","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"%23000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0V0z",fill:"none"}),(0,n.createElement)("path",{d:"M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"}))},{value:"chevron-double",label:(0,s.__)("Double Chevron","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",width:"24",height:"24","aria-hidden":"true",focusable:"false"},(0,n.createElement)("path",{d:"M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z"}))},{value:"arrow",label:(0,s.__)("Arrow","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"%23000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0V0z",fill:"none"}),(0,n.createElement)("path",{d:"M16.01 11H4v2h12.01v3L20 12l-3.99-4v3z"}))},{value:"triangle",label:(0,s.__)("Triangle","x3p0-breadcrumbs"),type:"mask",icon:(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000"},(0,n.createElement)("path",{d:"M0 0h24v24H0V0z",fill:"none"}),(0,n.createElement)("path",{d:"M10 17l5-5-5-5v10z"}))},{value:"slash",label:(0,s.__)("Slash","x3p0-breadcrumbs"),type:"text",icon:"/"},{value:"bar",label:(0,s.__)("Vertical Bar","x3p0-breadcrumbs"),type:"text",icon:"|"},{value:"middot",label:(0,s.__)("Middle Dot","x3p0-breadcrumbs"),type:"text",icon:"·"},{value:"black-circle",label:(0,s.__)("Circle: Filled","x3p0-breadcrumbs"),type:"text",icon:"●"},{value:"white-circle",label:(0,s.__)("Circle: Outlined","x3p0-breadcrumbs"),type:"text",icon:"○"}],m=window.wp.primitives,u=(0,n.createElement)(m.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,n.createElement)(m.Path,{d:"M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z"})),d=window.wp.components,b=({homePrefix:e,showHomeLabel:t,setAttributes:r})=>{(0,n.useEffect)((()=>{t||e||r({showHomeLabel:!0})}),[e]);const a=Array.from((0,i.applyFilters)("x3p0.breadcrumbs.home.prefixes",new Set(p))),l=(0,n.createElement)(d.BaseControl,{className:"x3p0-breadcrumbs-sep-picker",label:(0,s.__)("Home Icon","x3p0-ideas")},(0,n.createElement)("div",{className:"x3p0-breadcrumbs-sep-picker__description"},(0,s.__)("Pick an icon or symbol for the home breadcrumb item.","x3p0-ideas")),(0,n.createElement)(d.__experimentalGrid,{className:"x3p0-breadcrumbs-sep-picker__grid",columns:"6"},a.map(((t,a)=>((t,a)=>(0,n.createElement)(d.Button,{key:a,isPressed:e===t.value,className:"x3p0-breadcrumbs-sep-picker__button",label:t.label,showTooltip:!0,onClick:()=>r({homePrefix:t.value,homePrefixType:t.type})},"image"===t.type?t.icon:(0,n.createElement)("span",{className:"x3p0-breadcrumbs-sep-picker__button-text"},t.icon)))(t,a))))),o=(0,n.createElement)(d.ToggleControl,{label:(0,s.__)("Show home label","x3p0-breadcrumbs"),checked:t,onChange:()=>r({showHomeLabel:!t}),disabled:!e});return(0,n.createElement)(d.Dropdown,{className:"x3p0-breadcrumbs-sep-dropdown",contentClassName:"x3p0-breadcrumbs-sep-popover",focusOnMount:!0,popoverProps:{headerTitle:(0,s.__)("Home Icon","x3p0-ideas"),variant:"toolbar"},renderToggle:({isOpen:t,onToggle:r})=>(0,n.createElement)(d.ToolbarButton,{className:"x3p0-breadcrumbs-sep-dropdown__button",icon:u,label:(0,s.__)("Home Icon","x3p0-ideas"),onClick:r,"aria-expanded":t,isPressed:!!e}),renderContent:()=>(0,n.createElement)(d.Flex,{direction:"column",gap:"4"},l,o)})},h=(0,n.createElement)(m.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,n.createElement)(m.Path,{d:"M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z"})),x=({separator:e,setAttributes:t})=>{const r=Array.from((0,i.applyFilters)("x3p0.breadcrumbs.separators",new Set(c))),a=(0,n.createElement)(d.BaseControl,{className:"x3p0-breadcrumbs-sep-picker",label:(0,s.__)("Separator","x3p0-ideas")},(0,n.createElement)("div",{className:"x3p0-breadcrumbs-sep-picker__description"},(0,s.__)("Pick an icon or symbol that sits in between and separates breadcrumb items.","x3p0-ideas")),(0,n.createElement)(d.__experimentalGrid,{className:"x3p0-breadcrumbs-sep-picker__grid",columns:"6"},r.map(((r,a)=>((r,a)=>(0,n.createElement)(d.Button,{key:a,isPressed:e===r.value,className:"x3p0-breadcrumbs-sep-picker__button",label:r.label,showTooltip:!0,onClick:()=>t({separator:r.value,separatorType:r.type})},"image"===r.type?r.icon:(0,n.createElement)("span",{className:"x3p0-breadcrumbs-sep-picker__button-text"},r.icon)))(r,a)))));return(0,n.createElement)(d.Dropdown,{className:"x3p0-breadcrumbs-sep-dropdown",contentClassName:"x3p0-breadcrumbs-sep-popover",focusOnMount:!0,popoverProps:{headerTitle:(0,s.__)("Separator","x3p0-ideas"),variant:"toolbar"},renderToggle:({isOpen:t,onToggle:r})=>(0,n.createElement)(d.ToolbarButton,{className:"x3p0-breadcrumbs-sep-dropdown__button",icon:h,label:(0,s.__)("Separator","x3p0-ideas"),onClick:r,"aria-expanded":t,isPressed:!!e}),renderContent:()=>a})},v=window.wp.blockEditor;var g=r(184),_=r.n(g);const w=e=>e.preventDefault(),f=["left","center","right"],y=[{key:"html",name:(0,s.__)("Plain HTML","x3p0-breadcrumbs")},{key:"microdata",name:(0,s.__)("Microdata","x3p0-breadcrumbs")},{key:"rdfa",name:(0,s.__)("RDFa","x3p0-breadcrumbs")}];(0,a.registerBlockType)(l,{icon:o,edit:({attributes:{homePrefix:e,homePrefixType:t,justifyContent:r,markup:a,showHomeLabel:l,showOnHomepage:o,showTrailEnd:i,separator:p,separatorType:c},setAttributes:m})=>{const u=(0,n.createElement)(v.BlockControls,{group:"block"},(0,n.createElement)(v.JustifyContentControl,{allowedControls:f,value:r,onChange:e=>m({justifyContent:e}),popoverProps:{position:"bottom right",variant:"toolbar"}})),h=(0,n.createElement)(v.BlockControls,{group:"other"},(0,n.createElement)(b,{homePrefix:e,showHomeLabel:l,setAttributes:m}),(0,n.createElement)(x,{separator:p,setAttributes:m})),g=(0,n.createElement)(n.Fragment,null,u,h),E=(0,n.createElement)(d.ToggleControl,{label:(0,s.__)("Show on homepage","x3p0-breadcrumbs"),help:o?(0,s.__)("Breadcrumbs display on the homepage.","x3p0-breadcrumbs"):(0,s.__)("Breadcrumbs hidden on the homepage.","x3p0-breadcrumbs"),checked:o,onChange:()=>m({showOnHomepage:!o})}),k=(0,n.createElement)(d.ToggleControl,{label:(0,s.__)("Show last breadcrumb","x3p0-breadcrumbs"),help:i?(0,s.__)("Current page item is shown.","x3p0-breadcrumbs"):(0,s.__)("Current page item is hidden.","x3p0-breadcrumbs"),checked:i,onChange:()=>m({showTrailEnd:!i})}),C=(0,n.createElement)(d.CustomSelectControl,{label:(0,s.__)("Markup style","x3p0-breadcrumbs"),options:y,value:y.find((e=>e.key===a)),onChange:({selectedItem:e})=>m({markup:e.key})}),H=(0,n.createElement)(v.InspectorControls,{group:"settings"},(0,n.createElement)(d.PanelBody,{title:(0,s.__)("Breadcrumb settings","x3p0-breadcrumbs")},E,k,C)),B=(0,v.useBlockProps)({className:_()({breadcrumbs:!0,[`has-home-${t}-${e}`]:t&&e,[`has-sep-${c}-${p}`]:c&&p,[`is-content-justification-${r}`]:r})});let P=[{type:"home",label:(0,s.__)("Home","x3p0-breadcrumbs"),link:!0,hide:!l},{type:"post",label:(0,s.__)("Parent Page","x3p0-breadcrumbs"),link:!0,hide:!1}];i&&P.push({type:"post",label:(0,s.__)("Current Page","x3p0-breadcrumbs"),link:!1,hide:!1});const M=(0,n.createElement)("ol",{className:"breadcrumbs__trail"},P.map(((e,t)=>((e,t)=>{const r=e.link?"a":"span";return(0,n.createElement)("li",{key:t,className:`breadcrumbs__crumb breadcrumbs__crumb--${e.type}`},(0,n.createElement)(r,{href:e.link?"#breadcrumbs-pseudo-link":null,onClick:w,className:"breadcrumbs__crumb-content"},(0,n.createElement)("span",{className:"breadcrumbs__crumb-label "+(e.hide?"screen-reader-text":""),itemProp:"name"},e.label)))})(e,t))));return(0,n.createElement)(n.Fragment,null,g,H,(0,n.createElement)("nav",{...B},M))}})},184:(e,t)=>{var r;!function(){"use strict";var a={}.hasOwnProperty;function l(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var n=typeof r;if("string"===n||"number"===n)e.push(r);else if(Array.isArray(r)){if(r.length){var o=l.apply(null,r);o&&e.push(o)}}else if("object"===n){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var s in r)a.call(r,s)&&r[s]&&e.push(s)}}}return e.join(" ")}e.exports?(l.default=l,e.exports=l):void 0===(r=function(){return l}.apply(t,[]))||(e.exports=r)}()}},r={};function a(e){var l=r[e];if(void 0!==l)return l.exports;var n=r[e]={exports:{}};return t[e](n,n.exports,a),n.exports}a.m=t,e=[],a.O=(t,r,l,n)=>{if(!r){var o=1/0;for(c=0;c<e.length;c++){for(var[r,l,n]=e[c],s=!0,i=0;i<r.length;i++)(!1&n||o>=n)&&Object.keys(a.O).every((e=>a.O[e](r[i])))?r.splice(i--,1):(s=!1,n<o&&(o=n));if(s){e.splice(c--,1);var p=l();void 0!==p&&(t=p)}}return t}n=n||0;for(var c=e.length;c>0&&e[c-1][2]>n;c--)e[c]=e[c-1];e[c]=[r,l,n]},a.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return a.d(t,{a:t}),t},a.d=(e,t)=>{for(var r in t)a.o(t,r)&&!a.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},a.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={826:0,431:0};a.O.j=t=>0===e[t];var t=(t,r)=>{var l,n,[o,s,i]=r,p=0;if(o.some((t=>0!==e[t]))){for(l in s)a.o(s,l)&&(a.m[l]=s[l]);if(i)var c=i(a)}for(t&&t(r);p<o.length;p++)n=o[p],a.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return a.O(c)},r=globalThis.webpackChunkx3p0_breadcrumbs=globalThis.webpackChunkx3p0_breadcrumbs||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var l=a.O(void 0,[431],(()=>a(52)));l=a.O(l)})();