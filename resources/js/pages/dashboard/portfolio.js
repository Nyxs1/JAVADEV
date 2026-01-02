/**
 * Portfolio Dashboard JavaScript Module
 * 
 * This module handles all Portfolio-related interactions on the dashboard.
 * Uses event-driven architecture with namespaced events.
 * 
 * Event Namespaces:
 * - javadev:portfolio:open - Open wizard modal
 * - javadev:portfolio:close - Close wizard modal
 * - javadev:portfolio:step-change - Navigate between steps
 * - javadev:evidence:open - Open evidence modal
 * - javadev:evidence:close - Close evidence modal
 */

(function() {
    'use strict';

    // =========================================
    // CONSTANTS
    // =========================================
    const EVENTS = {
        PORTFOLIO_OPEN: 'javadev:portfolio:open',
        PORTFOLIO_CLOSE: 'javadev:portfolio:close',
        PORTFOLIO_STEP_CHANGE: 'javadev:portfolio:step-change',
        EVIDENCE_OPEN: 'javadev:evidence:open',
        EVIDENCE_CLOSE: 'javadev:evidence:close',
    };

    const SELECTORS = {
        OPEN_WIZARD_BTN: '[data-open-portfolio-wizard]',
        EDIT_PORTFOLIO_BTN: '[data-edit-portfolio]',
        OPEN_EVIDENCE_BTN: '[data-open-evidence-modal]',
        CLOSE_EVIDENCE_BTN: '[data-close-evidence-modal]',
        EVIDENCE_MODAL: '#evidence-modal',
        EVIDENCE_FORM: '#evidence-form',
        EVIDENCE_ITEM_TYPE: '#evidence-item-type',
        EVIDENCE_ITEM_ID: '#evidence-item-id',
        PUBLISH_WARNING_BTN: '[data-show-publish-warning]',
        HIDE_PUBLISH_WARNING_BTN: '[data-hide-publish-warning]',
    };

    // =========================================
    // EVIDENCE MODAL HANDLERS
    // =========================================
    function openEvidenceModal(itemType, itemId) {
        const modal = document.querySelector(SELECTORS.EVIDENCE_MODAL);
        const itemTypeInput = document.querySelector(SELECTORS.EVIDENCE_ITEM_TYPE);
        const itemIdInput = document.querySelector(SELECTORS.EVIDENCE_ITEM_ID);
        
        if (!modal || !itemTypeInput || !itemIdInput) return;
        
        itemTypeInput.value = itemType;
        itemIdInput.value = itemId;
        modal.classList.remove('hidden');
        
        // Lock scroll
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        const firstInput = modal.querySelector('select, input');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent(EVENTS.EVIDENCE_OPEN, {
            detail: { itemType, itemId }
        }));
    }

    function closeEvidenceModal() {
        const modal = document.querySelector(SELECTORS.EVIDENCE_MODAL);
        const form = document.querySelector(SELECTORS.EVIDENCE_FORM);
        
        if (!modal) return;
        
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        if (form) {
            form.reset();
        }
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent(EVENTS.EVIDENCE_CLOSE));
    }

    // =========================================
    // PORTFOLIO WIZARD HANDLERS
    // =========================================
    function openPortfolioWizard(portfolioData = null) {
        window.dispatchEvent(new CustomEvent(EVENTS.PORTFOLIO_OPEN, {
            detail: portfolioData
        }));
    }

    function closePortfolioWizard() {
        window.dispatchEvent(new CustomEvent(EVENTS.PORTFOLIO_CLOSE));
    }

    // =========================================
    // PUBLISH WARNING MODAL HANDLERS
    // =========================================
    function showPublishWarningModal(courseId) {
        const modal = document.getElementById(`publish-warning-${courseId}`);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function hidePublishWarningModal(courseId) {
        const modal = document.getElementById(`publish-warning-${courseId}`);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // =========================================
    // EVENT BINDINGS
    // =========================================
    function bindEvents() {
        // Portfolio Wizard - Open (new portfolio)
        document.querySelectorAll(SELECTORS.OPEN_WIZARD_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                openPortfolioWizard(null);
            });
        });

        // Portfolio Wizard - Edit (existing portfolio)
        document.querySelectorAll(SELECTORS.EDIT_PORTFOLIO_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const portfolioJson = btn.dataset.portfolioJson;
                if (portfolioJson) {
                    try {
                        const data = JSON.parse(portfolioJson);
                        openPortfolioWizard(data);
                    } catch (err) {
                        console.error('Failed to parse portfolio data:', err);
                    }
                }
            });
        });

        // Evidence Modal - Open
        document.querySelectorAll(SELECTORS.OPEN_EVIDENCE_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const { itemType, itemId } = btn.dataset;
                if (itemType && itemId) {
                    openEvidenceModal(itemType, itemId);
                }
            });
        });

        // Evidence Modal - Close
        document.querySelectorAll(SELECTORS.CLOSE_EVIDENCE_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                closeEvidenceModal();
            });
        });

        // Evidence Modal - Close on overlay click
        const evidenceModal = document.querySelector(SELECTORS.EVIDENCE_MODAL);
        if (evidenceModal) {
            evidenceModal.addEventListener('click', (e) => {
                if (e.target === evidenceModal || e.target.closest('[data-close-evidence-overlay]')) {
                    closeEvidenceModal();
                }
            });
        }

        // Publish Warning Modal - Show
        document.querySelectorAll(SELECTORS.PUBLISH_WARNING_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const courseId = btn.dataset.courseId;
                if (courseId) {
                    showPublishWarningModal(courseId);
                }
            });
        });

        // Publish Warning Modal - Hide
        document.querySelectorAll(SELECTORS.HIDE_PUBLISH_WARNING_BTN).forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const courseId = btn.dataset.courseId;
                if (courseId) {
                    hidePublishWarningModal(courseId);
                }
            });
        });

        // Global ESC key handler for modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeEvidenceModal();
                // Portfolio wizard has its own ESC handler via Alpine
            }
        });

        // Confirm delete forms
        document.querySelectorAll('[data-confirm-delete]').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!confirm('Yakin hapus portfolio ini?')) {
                    e.preventDefault();
                }
            });
        });
    }

    // =========================================
    // INITIALIZATION
    // =========================================
    function init() {
        bindEvents();
        console.log('✅ Portfolio module initialized');
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose for debugging (not for usage in templates)
    window.__JAVADEV_PORTFOLIO_DEBUG__ = {
        EVENTS,
        openEvidenceModal,
        closeEvidenceModal,
        openPortfolioWizard,
    };
})();
