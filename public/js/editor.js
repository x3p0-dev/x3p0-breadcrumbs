(()=>{var e,r={712:(e,r,t)=>{"use strict";var n=t(184),o=t.n(n),i=t(893);function s(e,r){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);r&&(n=n.filter((function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable}))),t.push.apply(t,n)}return t}function a(e){for(var r=1;r<arguments.length;r++){var t=null!=arguments[r]?arguments[r]:{};r%2?s(Object(t),!0).forEach((function(r){c(e,r,t[r])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):s(Object(t)).forEach((function(r){Object.defineProperty(e,r,Object.getOwnPropertyDescriptor(t,r))}))}return e}function c(e,r,t){return r in e?Object.defineProperty(e,r,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[r]=t,e}var p=wp.coreData.store,l=wp.i18n.__,u=wp.data.useSelect,m=wp.components,b=m.PanelBody,d=m.ToggleControl,h=wp.blockEditor,f=h.BlockControls,g=h.InspectorControls,y=h.JustifyContentControl,j=h.useBlockProps,x=function(e){return e.preventDefault()},v=["left","center","right"];const O=(0,i.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg","enable-background":"new 0 0 24 24",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#000000",children:[(0,i.jsx)("g",{children:(0,i.jsx)("rect",{fill:"none",height:"24",width:"24"})}),(0,i.jsx)("g",{children:(0,i.jsxs)("g",{children:[(0,i.jsx)("polygon",{points:"15.5,5 11,5 16,12 11,19 15.5,19 20.5,12"}),(0,i.jsx)("polygon",{points:"8.5,5 0,5 0,12 0,19 8.5,19 13.5,12"})]})})]});const _=JSON.parse('{"apiVersion":2,"name":"x3p0/breadcrumbs","title":"X3P0 Breadcrumbs","category":"widgets","description":"Add a breadcrumb trail back to the site homepage. Breadcrumb items appear as placeholders in the editor and will populate with the correct data on the site front end.","keywords":["breadcrumb","trail","navigation"],"textdomain":"x3p0-breadcrumbs","icon":"","example":{},"attributes":{"itemsJustification":{"type":"string","default":""},"showOnHomepage":{"type":"boolean","default":false},"showTrailEnd":{"type":"boolean","default":true}},"supports":{"anchor":true,"align":true,"html":false,"__experimentalBorder":true,"color":{"gradients":true,"link":true},"spacing":{"margin":true,"padding":true},"typography":{"fontSize":true,"lineHeight":true,"__experimentalFontStyle":true,"__experimentalFontWeight":true,"__experimentalFontFamily":true,"__experimentalTextTransform":true}},"style":"x3p0-breadcrumbs","editorStyle":"x3p0-breadcrumbs-editor","editorScript":"x3p0-breadcrumbs-editor"}');var w={icon:O,edit:function(e){var r=e.attributes,t=e.setAttributes,n=e.clientId,s=r.itemsJustification,m=r.showOnHomepage,h=r.showTrailEnd,O=u((function(e){var r;return{homeUrl:null===(r=(0,e(p).getUnstableBase)())||void 0===r?void 0:r.home}}),[n]).homeUrl,_=j({className:o()(c({breadcrumbs:!0},"items-justified-".concat(s),s))});return(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(f,{group:"block",children:(0,i.jsx)(y,{allowedControls:v,value:s,onChange:function(e){return t({itemsJustification:e})},popoverProps:{position:"bottom right",isAlternate:!0}})}),(0,i.jsx)(g,{children:(0,i.jsxs)(b,{title:l("Breadcrumb settings","x3p0-breadcrumbs"),children:[(0,i.jsx)(d,{label:l("Show on homepage","x3p0-breadcrumbs"),help:l(m?"Breadcrumbs display on the homepage.":"Breadcrumbs hidden on the homepage.","x3p0-breadcrumbs"),checked:m,onChange:function(){return t({showOnHomepage:!m})}}),(0,i.jsx)(d,{label:l("Show last breadcrumb","x3p0-breadcrumbs"),help:l(h?"Current page item is shown.":"Current page item is hidden.","x3p0-breadcrumbs"),checked:h,onChange:function(){return t({showTrailEnd:!h})}})]})}),(0,i.jsx)("nav",a(a({},_),{},{children:(0,i.jsxs)("ul",{class:"breadcrumbs__trail",itemscope:"",itemtype:"https://schema.org/BreadcrumbList",children:[(0,i.jsxs)("li",{class:"breadcrumbs__crumb breadcrumbs__crumb--home",itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem",children:[(0,i.jsx)("a",{href:O,onClick:x,class:"breadcrumbs__crumb-content",itemprop:"item",children:(0,i.jsx)("span",{itemprop:"name",children:l("Home","x3p0-breadcrumbs")})}),(0,i.jsx)("meta",{itemprop:"position",content:"1"})]}),(0,i.jsxs)("li",{class:"breadcrumbs__crumb breadcrumbs__crumb--post",itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem",children:[(0,i.jsx)("a",{href:"#",onClick:x,class:"breadcrumbs__crumb-content",itemprop:"item",children:(0,i.jsx)("span",{itemprop:"name",children:l("Parent Crumb","x3p0-breadcrumbs")})}),(0,i.jsx)("meta",{itemprop:"position",content:"2"})]}),h&&(0,i.jsxs)("li",{class:"breadcrumbs__crumb breadcrumbs__crumb--post",itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem",children:[(0,i.jsx)("span",{class:"breadcrumbs__crumb-content",itemscope:"",itemtype:"https://schema.org/WebPage",itemprop:"item",children:(0,i.jsx)("span",{itemprop:"name",children:l("Current Crumb","x3p0-breadcrumbs")})}),(0,i.jsx)("meta",{itemprop:"position",content:"3"})]})]})}))]})},save:function(){return null}},k=wp.blocks.registerBlockType;wp.domReady((function(){k(_,w)}))},184:(e,r)=>{var t;!function(){"use strict";var n={}.hasOwnProperty;function o(){for(var e=[],r=0;r<arguments.length;r++){var t=arguments[r];if(t){var i=typeof t;if("string"===i||"number"===i)e.push(t);else if(Array.isArray(t)){if(t.length){var s=o.apply(null,t);s&&e.push(s)}}else if("object"===i)if(t.toString===Object.prototype.toString)for(var a in t)n.call(t,a)&&t[a]&&e.push(a);else e.push(t.toString())}}return e.join(" ")}e.exports?(o.default=o,e.exports=o):void 0===(t=function(){return o}.apply(r,[]))||(e.exports=t)}()},644:()=>{},857:()=>{},251:(e,r,t)=>{"use strict";t(424);var n=t(804),o=60103;if(r.Fragment=60107,"function"==typeof Symbol&&Symbol.for){var i=Symbol.for;o=i("react.element"),r.Fragment=i("react.fragment")}var s=n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,a=Object.prototype.hasOwnProperty,c={key:!0,ref:!0,__self:!0,__source:!0};function p(e,r,t){var n,i={},p=null,l=null;for(n in void 0!==t&&(p=""+t),void 0!==r.key&&(p=""+r.key),void 0!==r.ref&&(l=r.ref),r)a.call(r,n)&&!c.hasOwnProperty(n)&&(i[n]=r[n]);if(e&&e.defaultProps)for(n in r=e.defaultProps)void 0===i[n]&&(i[n]=r[n]);return{$$typeof:o,type:e,key:p,ref:l,props:i,_owner:s.current}}r.jsx=p,r.jsxs=p},893:(e,r,t)=>{"use strict";e.exports=t(251)},424:e=>{"use strict";var r=Object.getOwnPropertySymbols,t=Object.prototype.hasOwnProperty,n=Object.prototype.propertyIsEnumerable;function o(e){if(null==e)throw new TypeError("Object.assign cannot be called with null or undefined");return Object(e)}e.exports=function(){try{if(!Object.assign)return!1;var e=new String("abc");if(e[5]="de","5"===Object.getOwnPropertyNames(e)[0])return!1;for(var r={},t=0;t<10;t++)r["_"+String.fromCharCode(t)]=t;if("0123456789"!==Object.getOwnPropertyNames(r).map((function(e){return r[e]})).join(""))return!1;var n={};return"abcdefghijklmnopqrst".split("").forEach((function(e){n[e]=e})),"abcdefghijklmnopqrst"===Object.keys(Object.assign({},n)).join("")}catch(e){return!1}}()?Object.assign:function(e,i){for(var s,a,c=o(e),p=1;p<arguments.length;p++){for(var l in s=Object(arguments[p]))t.call(s,l)&&(c[l]=s[l]);if(r){a=r(s);for(var u=0;u<a.length;u++)n.call(s,a[u])&&(c[a[u]]=s[a[u]])}}return c}},804:e=>{"use strict";e.exports=React}},t={};function n(e){var o=t[e];if(void 0!==o)return o.exports;var i=t[e]={exports:{}};return r[e](i,i.exports,n),i.exports}n.m=r,e=[],n.O=(r,t,o,i)=>{if(!t){var s=1/0;for(p=0;p<e.length;p++){for(var[t,o,i]=e[p],a=!0,c=0;c<t.length;c++)(!1&i||s>=i)&&Object.keys(n.O).every((e=>n.O[e](t[c])))?t.splice(c--,1):(a=!1,i<s&&(s=i));a&&(e.splice(p--,1),r=o())}return r}i=i||0;for(var p=e.length;p>0&&e[p-1][2]>i;p--)e[p]=e[p-1];e[p]=[t,o,i]},n.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return n.d(r,{a:r}),r},n.d=(e,r)=>{for(var t in r)n.o(r,t)&&!n.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},n.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={103:0,826:0,938:0};n.O.j=r=>0===e[r];var r=(r,t)=>{var o,i,[s,a,c]=t,p=0;for(o in a)n.o(a,o)&&(n.m[o]=a[o]);if(c)var l=c(n);for(r&&r(t);p<s.length;p++)i=s[p],n.o(e,i)&&e[i]&&e[i][0](),e[s[p]]=0;return n.O(l)},t=self.webpackChunkx3p0_breadcrumbs=self.webpackChunkx3p0_breadcrumbs||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})(),n.O(void 0,[826,938],(()=>n(712))),n.O(void 0,[826,938],(()=>n(644)));var o=n.O(void 0,[826,938],(()=>n(857)));o=n.O(o)})();