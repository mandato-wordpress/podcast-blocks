/**
 * Podcast Blocks – Podcast Episode block.
 *
 * Source file — compiled by @wordpress/scripts into build/podcast-episode/.
 * Run `npm run build` (production) or `npm start` (watch) to regenerate.
 */

import './style-index.css';

import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button,
	Placeholder,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { Fragment, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import metadata from './block.json';

// ─────────────────────────────────────────────────────────────────────────────
// Edit component
// ─────────────────────────────────────────────────────────────────────────────

function PodcastEpisodeEdit( { attributes, setAttributes } ) {
	const { mediaUrl, mediaId, mediaType, mediaTitle, transcriptUrl, transcriptId } = attributes;

	const blockProps = useBlockProps( { className: 'podcast-episode-editor' } );

	const postId = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostId(),
		[]
	);
	const { editPost } = useDispatch( 'core/editor' );

	// Keep post meta in sync so the save_post PHP hook can write `enclosure`.
	useEffect( () => {
		if ( ! postId ) {
			return;
		}
		editPost( {
			meta: {
				_podcast_media_url: mediaUrl || '',
				_podcast_media_type: mediaType || 'audio',
			},
		} );
	}, [ mediaUrl, mediaType, postId ] ); // eslint-disable-line react-hooks/exhaustive-deps

	// ── Handlers ──────────────────────────────────────────────────────────

	function onSelectMedia( media ) {
		const mime = media.mime || media.mime_type || '';
		const isVideo = mime.startsWith( 'video' );
		const type = isVideo ? 'video' : 'audio';

		setAttributes( {
			mediaUrl: media.url,
			mediaId: media.id,
			mediaType: type,
			mediaTitle: media.title || '',
		} );

		if ( postId ) {
			editPost( {
				meta: {
					_podcast_media_url: media.url,
					_podcast_media_type: type,
					_podcast_media_mime: mime,
					_podcast_media_size: media.filesizeInBytes || 0,
				},
			} );
		}
	}

	function onRemoveMedia() {
		setAttributes( {
			mediaUrl: '',
			mediaId: 0,
			mediaType: 'audio',
			mediaTitle: '',
		} );
		if ( postId ) {
			editPost( {
				meta: {
					_podcast_media_url: '',
					_podcast_media_type: 'audio',
					_podcast_media_mime: '',
					_podcast_media_size: 0,
				},
			} );
		}
	}

	function onSelectTranscript( media ) {
		setAttributes( {
			transcriptUrl: media.url,
			transcriptId: media.id,
		} );
		if ( postId ) {
			editPost( {
				meta: {
					_podcast_transcript_url: media.url,
					_podcast_transcript_id: media.id,
				},
			} );
		}
	}

	function onRemoveTranscript() {
		setAttributes( { transcriptUrl: '', transcriptId: 0 } );
		if ( postId ) {
			editPost( {
				meta: {
					_podcast_transcript_url: '',
					_podcast_transcript_id: 0,
				},
			} );
		}
	}

	function onChangeUrl( value ) {
		const isVideo = /\.(mp4|m4v|webm|ogv|mov)(\?.*)?$/i.test( value );
		setAttributes( {
			mediaUrl: value,
			mediaId: 0,
			mediaType: isVideo ? 'video' : 'audio',
		} );
	}

	// ── Inspector sidebar ──────────────────────────────────────────────────

	const inspectorControls = (
		<InspectorControls>
			<PanelBody
				title={ __( 'Episode Media', 'podcast-blocks' ) }
				initialOpen={ true }
			>
				<TextControl
					label={ __( 'Media URL', 'podcast-blocks' ) }
					help={ __(
						'Enter a URL or use the Upload button to choose from the media library.',
						'podcast-blocks'
					) }
					value={ mediaUrl }
					onChange={ onChangeUrl }
					type="url"
					__nextHasNoMarginBottom
				/>
				<SelectControl
					label={ __( 'Media Type', 'podcast-blocks' ) }
					value={ mediaType }
					options={ [
						{
							label: __( 'Audio', 'podcast-blocks' ),
							value: 'audio',
						},
						{
							label: __( 'Video', 'podcast-blocks' ),
							value: 'video',
						},
					] }
					onChange={ ( value ) =>
						setAttributes( { mediaType: value } )
					}
					__nextHasNoMarginBottom
				/>
				{ mediaTitle && (
					<p
						style={ {
							fontSize: '12px',
							color: '#757575',
							marginTop: '8px',
						} }
					>
						{ __( 'File: ', 'podcast-blocks' ) + mediaTitle }
					</p>
				) }
			</PanelBody>
			<PanelBody
				title={ __( 'Episode Transcript', 'podcast-blocks' ) }
				initialOpen={ false }
			>
				<p style={ { fontSize: '12px', color: '#757575', marginBottom: '8px' } }>
					{ __( 'Upload an SRT or VTT transcript file. It will be linked in the podcast RSS feed.', 'podcast-blocks' ) }
				</p>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectTranscript }
						allowedTypes={ [ 'application/x-subrip', 'text/vtt' ] }
						value={ transcriptId || undefined }
						render={ ( { open } ) => (
							<Button
								onClick={ open }
								variant={ transcriptUrl ? 'secondary' : 'primary' }
								icon={ transcriptUrl ? 'update' : 'upload' }
							>
								{ transcriptUrl
									? __( 'Replace Transcript', 'podcast-blocks' )
									: __( 'Upload Transcript', 'podcast-blocks' ) }
							</Button>
						) }
					/>
				</MediaUploadCheck>
				{ transcriptUrl && (
					<Fragment>
						<p style={ { fontSize: '12px', color: '#757575', marginTop: '8px' } }>
							{ __( 'File: ', 'podcast-blocks' ) + transcriptUrl.split( '/' ).pop() }
						</p>
						<Button
							onClick={ onRemoveTranscript }
							variant="tertiary"
							isDestructive
							style={ { marginTop: '4px' } }
						>
							{ __( 'Remove Transcript', 'podcast-blocks' ) }
						</Button>
					</Fragment>
				) }
			</PanelBody>
		</InspectorControls>
	);

	// ── Upload / Replace button ────────────────────────────────────────────
	const uploadButton = (
		<MediaUploadCheck>
			<MediaUpload
				onSelect={ onSelectMedia }
				allowedTypes={ [ 'audio', 'video' ] }
				value={ mediaId || undefined }
				render={ ( { open } ) => (
					<Button
						onClick={ open }
						variant={ mediaUrl ? 'secondary' : 'primary' }
						icon={ mediaUrl ? 'update' : 'upload' }
					>
						{ mediaUrl
							? __( 'Replace Media', 'podcast-blocks' )
							: __( 'Upload / Select Media', 'podcast-blocks' ) }
					</Button>
				) }
			/>
		</MediaUploadCheck>
	);

	// ── Click on download / subscribe link ─────────────────────────────────
	const handleLinkClick = ( e ) => {
		e.preventDefault();
	};

	// ── Placeholder (no media) ─────────────────────────────────────────────

	if ( ! mediaUrl ) {
		return (
			<Fragment>
				{ inspectorControls }
				<div { ...blockProps }>
					<Placeholder
						icon="controls-play"
						label={ __( 'Podcast Episode', 'podcast-blocks' ) }
						instructions={ __(
							'Upload or select an audio/video file, or enter a URL in the sidebar panel.',
							'podcast-blocks'
						) }
					>
						{ uploadButton }
					</Placeholder>
				</div>
			</Fragment>
		);
	}

	// ── Player + link-bar preview ──────────────────────────────────────────

	const player =
		mediaType === 'video' ? (
			<video
				controls
				src={ mediaUrl }
				className="podcast-episode-player podcast-episode-player-video"
			/>
		) : (
			<audio
				controls
				src={ mediaUrl }
				className="podcast-episode-player podcast-episode-player-audio"
			/>
		);

	return (
		<Fragment>
			{ inspectorControls }
			<div { ...blockProps }>
				{ player }

				{ /* Link-bar preview — mirrors the frontend layout; non-interactive in editor */ }
				<div className="podcast-episode-links">
					<button
						onClick={ handleLinkClick }
						style={ {
							all: 'unset',
							cursor: 'pointer',
							textDecoration: 'underline',
							color: '#069',
						} }
					>
						<span>{ __( 'Download', 'podcast-blocks' ) }</span>
					</button>{ ' ' }
					|
					<button
						onClick={ handleLinkClick }
						style={ {
							all: 'unset',
							cursor: 'pointer',
							textDecoration: 'underline',
							color: '#069',
						} }
					>
						<span>{ __( 'Subscribe', 'podcast-blocks' ) }</span>
					</button>
				</div>

				{ /* Replace / Remove actions */ }
				<div className="podcast-episode-editor-actions">
					{ uploadButton }
					<Button
						onClick={ onRemoveMedia }
						variant="tertiary"
						isDestructive
						icon="trash"
						style={ { marginLeft: '8px' } }
					>
						{ __( 'Remove Media', 'podcast-blocks' ) }
					</Button>
				</div>
			</div>
		</Fragment>
	);
}

// ─────────────────────────────────────────────────────────────────────────────
// Block registration
// ─────────────────────────────────────────────────────────────────────────────

registerBlockType( metadata.name, {
	edit: PodcastEpisodeEdit,
	save: () => null, // dynamic block – rendered by PHP
} );

// eof
