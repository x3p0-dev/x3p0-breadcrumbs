<?php
// This file is generated. Do not modify it manually.
return array(
	'breadcrumbs' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'version' => '20250908',
		'name' => 'x3p0/breadcrumbs',
		'title' => 'Breadcrumbs',
		'category' => 'widgets',
		'description' => 'Add a breadcrumb trail back to the site homepage. Breadcrumb items appear as placeholders in the editor and will populate with the correct data on the site front end.',
		'keywords' => array(
			'breadcrumb',
			'trail',
			'navigation'
		),
		'attributes' => array(
			'justifyContent' => array(
				'type' => 'string',
				'default' => ''
			),
			'showHomeLabel' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showOnHomepage' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showTrailStart' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showTrailEnd' => array(
				'type' => 'boolean',
				'default' => true
			),
			'homePrefix' => array(
				'type' => 'string',
				'default' => ''
			),
			'homePrefixType' => array(
				'type' => 'string',
				'default' => ''
			),
			'labels' => array(
				'type' => 'object',
				'default' => array(
					
				)
			),
			'mapRewriteTags' => array(
				'type' => 'object',
				'default' => array(
					'post' => false
				)
			),
			'markup' => array(
				'type' => 'string',
				'default' => 'rdfa'
			),
			'postTaxonomy' => array(
				'type' => 'object',
				'default' => array(
					'post' => 'category'
				)
			),
			'separator' => array(
				'type' => 'string',
				'default' => 'chevron'
			),
			'separatorType' => array(
				'type' => 'string',
				'default' => 'mask'
			)
		),
		'supports' => array(
			'anchor' => true,
			'align' => true,
			'html' => false,
			'__experimentalBorder' => array(
				'radius' => true,
				'color' => true,
				'width' => true,
				'style' => true,
				'__experimentalDefaultControls' => array(
					'width' => true,
					'color' => true
				)
			),
			'__experimentalStyle' => array(
				'css' => '&.is-layout-flex { gap: var(--wp--custom--x-3-p-0-breadcrumbs--gap, 0.5rem); }',
				'spacing' => array(
					'blockGap' => 'var(--wp--custom--x-3-p-0-breadcrumbs--gap, 0.5rem)'
				)
			),
			'color' => array(
				'link' => true,
				'gradients' => true,
				'__experimentalDefaultControls' => array(
					'background' => true,
					'text' => true,
					'link' => true
				)
			),
			'layout' => array(
				'allowCustomContentAndWideSize' => false,
				'allowEditing' => true,
				'allowInheriting' => false,
				'allowJustification' => false,
				'allowOrientation' => false,
				'allowSizingOnChildren' => false,
				'allowSwitching' => false,
				'allowVerticalAlignment' => false,
				'default' => array(
					'type' => 'flex',
					'flexWrap' => 'wrap',
					'orientation' => 'horizontal'
				)
			),
			'spacing' => array(
				'blockGap' => true,
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'padding' => true
				)
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'__experimentalFontStyle' => true,
				'__experimentalFontWeight' => true,
				'__experimentalFontFamily' => true,
				'__experimentalLetterSpacing' => true,
				'__experimentalTextTransform' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true
				)
			)
		),
		'textdomain' => 'x3p0-breadcrumbs',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'example' => array(
			
		)
	)
);
