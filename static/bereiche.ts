page {  
  config {
    // We don't want any other HTML title tag
    noPageTitle = 2

    // Spam Protection
    spamProtectEmailAddresses = 6
    spamProtectEmailAddresses_atSubst       = <span style="display:none;">spamfilter</span><span class="dummy">@</span>
    spamProtectEmailAddresses_lastDotSubst  = <span style="display:none;">spamfilter</span><span class="dummy">.</span>
  }

  // SEO - Search Engine Optimization - HTML meta tags generated by the browser
  meta {
    description {
      field >
      data = register:browser_description
    }
    keywords {
      field >
      data = register:browser_keywords
    }
  }

  includeCSS {
    browser_bestellwesen = EXT:he_best/res/bestellwesen.css
  }
}

plugin.tx_browser_pi1 {
  
  template {
    file = EXT:he_tools/res/bereiche.tmpl
  }
  
  displayList {
    seo {
      htmlHead {
        title = 1
        meta {
          description = 1
          keywords    = 1
        }
      }
    }
    image.file.maxW = 200
    image.file.maxH = 200
  }

  displaySingle {
    seo {
      htmlHead {
        title = 1
        meta {
          description = 1
          description {
            crop = 200 | ... | 1
          }
          keywords    = 1
        }
      }
    }
 
    // [Boolean] If first image is for the preview, it wouldn't display in the single view
    firstImageIsPreview       = 0
    // [Boolean] If first image is for the preview, but there is no further image, preview image will displayed in the single view
    forceFirstImageIsPreview  = 1
  }
	views >
  views {
    list {
      1 {
	      select (
	        br.uid                  AS 'br.uid',
          br.title			          AS 'br.title', 
          br.email		      			AS 'br.email',
          br.telefon			      	AS 'br.telefon',
          br.bemerkung      			AS 'br.bemerkung',
          k1.title                AS 'k1.title', 
          k2.title                AS 'k2.title'
        )
        search (
          br.title,
          br.bemerkung,
          k1.title,
          k2.title
        )       
        from {
          table = tx_hetools_bereich
          alias = br
        }
        andWhere = br.pid IN (###PID_LIST###)
        joins {
          // kategorie1
          0 {
            type  = LEFT JOIN
            table = tx_hetools_kategorie1
            alias = k1
            on    = br.kategorie1 = k1.uid
          }
          // kategorie2
          1 {
            type  = LEFT JOIN
            table = tx_hetools_kategorie2
            alias = k2
            on    = br.kategorie2 = k2.uid
          }
        }
        aliases {
          tables {
            br = tx_hetools_bereich
            k1 = tx_hetools_kategorie1
            k2 = tx_hetools_kategorie2
          }
          fields {
            uid     = br.uid
          }
        }


       	csvLinkToSingleView = tx_hetools_bereich.uid
        tx_hetools_bereich.title {
        	wrap = |
        	if.isTrue = ###TX_HETOOLS_BEREICH.TITLE###
        }
        tx_hetools_bereich.email {
        	wrap = <br>E-Mail:&nbsp;|
        	if.isTrue = ###TX_HETOOLS_BEREICH.EMAIL###
        }
        tx_hetools_bereich.telefon {
        	wrap = <br>Telefon:&nbsp;|
        	if.isTrue = ###TX_HETOOLS_BEREICH.TELEFON###
        }
        tx_hetools_bereich.bemerkung {
        	wrap = <br>|
        	if.isTrue = ###TX_HETOOLS_BEREICH.BEMERKUNG###
        }

        filter {
          tx_hetools_kategorie1 {
            title < plugin.tx_browser_pi1.displayList.master_templates.selectbox
            title.wrap = <div class="selectbox"><span class="selectbox_title">Standort</span>|</div>
            title.wrap.object (
              <select name="###TABLE.FIELD###" id="###ID###" size="###SIZE###"
                      onchange="javascript:document.forms['filter_form'].submit()">|</select>
            )
            title.sql.andWhere = pid IN (###PID_LIST###)
          }
          tx_hetools_kategorie2 {
            title < plugin.tx_browser_pi1.displayList.master_templates.selectbox
            title.wrap = <div class="selectbox"><span class="selectbox_title">Anliegen</span>|</div>
            title.wrap.object (
              <select name="###TABLE.FIELD###" id="###ID###" size="###SIZE###"
                      onchange="javascript:document.forms['filter_form'].submit()">|</select>
            )
            title.sql.andWhere = pid IN (###PID_LIST###)
          }
        }
      }
    }
    single {
    	1 >
      1 {
	      select (
	        br.uid                  AS 'br.uid',
          br.title			          AS 'br.title', 
          br.email		      			AS 'br.email',
          br.telefon			      	AS 'br.tel',
          br.bemerkung      			AS 'br.bemerkung',
          k1.title                AS 'k1.title', 
          k2.title                AS 'k2.title'
        )
        search (
          br.title,
          br.bemerkung,
          k1.title,
          k2.title
        )       
        from {
          table = tx_hetools_bereich
          alias = er
        }
        andWhere = br.pid IN (###PID_LIST###)
        joins {
          // kategorie1
          0 {
            type  = LEFT JOIN
            table = tx_hetools_kategorie1
            alias = k1
            on    = br.kategorie1 = k1.uid
          }
          // kategorie2
          1 {
            type  = LEFT JOIN
            table = tx_hetools_kategorie2
            alias = k2
            on    = br.kategorie2 = k2.uid
          }
        }
        aliases {
          tables {
            er = tx_hetools_bereich
            k1 = tx_hetools_kategorie1
            k2 = tx_hetools_kategorie2
          }
          fields {
            uid     = br.uid
          }
        }
      }
    }
  }
}