{* $Id: tiki-accounting_books.tpl 58749 2016-06-01 01:39:05Z lindonb $ *}
{tabset}
	{tab name="{tr}My books{/tr}"}
		<h2>{tr}My books{/tr}</h2>
		<div id="booklist" class="table-responsive">
			<table class="table">
				<tr>
					<th>{tr}Id{/tr}</th>
					<th>{tr}Name{/tr}</th>
					<th>{tr}Start date{/tr}</th>
					<th>{tr}End date{/tr}</th>
					<th>{tr}Currency{/tr}</th>
					<th>{tr}Tax automation{/tr}</th>
					<th>{tr}Status{/tr}</th>
				</tr>
				{foreach item=element from=$books}
					<tr>
						<td><a href="tiki-accounting.php?bookId={$element.bookId}">{$element.bookId}</a></td>
						<td><a href="tiki-accounting.php?bookId={$element.bookId}">{$element.bookName}</a></td>
						<td>{$element.bookStartDate|tiki_short_datetime}</td>
						<td>{$element.bookEndDate|tiki_short_datetime}</td>
						<td>{$element.bookCurrency}
							({if $element.bookCurrencyPos==-1}{tr}before{/tr}{elseif $element.bookCurrencyPos==0}{tr}hide{/tr}{else}{tr}behind{/tr}){/if})
						</td>
						<td>{$element.taxAutomation}</td>
						<td>
							{if $element.bookClosed=='y'}{tr}closed{/tr}{else}{tr}open{/tr}
								{if $canCreate}
									<a class="icon" href="tiki-accounting_books.php?action=close&bookId={$element.bookId}">
										{icon name="file-archive" _confirm="{tr}Are you sure, you want to close this book{/tr}" alt="{tr}close book{/tr}"}
									</a>
								{/if}
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/tab}
	{if $canCreate}
		{tab name="{tr}Create a book{/tr}"}
			<h2>{tr}Create a book{/tr}</h2>
			<div id="createbookform">
				<form action="tiki-accounting_books.php" method="post">
					<input type="hidden" name="action" value="create">
					<input type="hidden" name="bookClosed" id="bookClosed" value="n">
					<h1>{tr}Create a book{/tr}</h1>
					<fieldset>
						<legend>{tr}Book properties{/tr}</legend>
						<div>
							<label class="aclabel">{tr}Name of the book{/tr}</label>
							<input type="text" name="bookName" id="bookName" value="{$bookName}">
						</div>
						<div>
							<label class="aclabel">{tr}First date in journal{/tr}</label>
							<input type="text" name="bookStartDate" id="bookStartDate" value="{$bookStartDate}">
						</div>
						<div>
							<label class="aclabel">{tr}Last date in journal{/tr}</label>
							<input type="text" name="bookEndDate" id="bookEndDate" value="{$bookEndDate}">
						</div>
					</fieldset>
					<fieldset>
						<legend>{tr}Currency settings{/tr}</legend>
						<div>
							<label class="aclabel">{tr}Currency (up to 3 letters) {/tr}</label>
							<input type="text" name="bookCurrency" id="bookCurrency" value="{$bookCurrency}">
						</div>
						<div>
							<label class="aclabel">{tr}Position of the currency {/tr}</label>
							<select name="bookCurrencyPos" id="bookCurrencyPos">
								<option value="0"{if $bookCurrencyPos==0} selected="selected"{/if}>{tr}Show no currency{/tr}</option>
								<option value="-1"{if $bookCurrencyPos==-1} selected="selected"{/if}>{tr}Show currency in front of numbers{/tr}</option>
								<option value="1"{if $bookCurrencyPos==1} selected="selected"{/if}>{tr}Show currency behind numbers{/tr}</option>
							</select>
						</div>
						<div>
							<label class="aclabel">{tr}Decimals{/tr}</label>
							<input type="text" name="bookDecimals" id="bookDecimals" value="{$bookDecimals}">
						</div>
						<div>
							<label class="aclabel">{tr}Decimal point{/tr}</label>
							<select name="bookDecPoint" id="bookDecPoint">
								<option value="," {if $bookDecPoint eq ','}selected="selected"{/if}>{tr}Comma{/tr}</option>
								<option value="." {if empty($bookDecPoint) or $bookDecPoint eq '.'}selected="selected"{/if}>{tr}Decimal{/tr}</option>
								<option value=" " {if $bookDecPoint eq ' '}selected="selected"{/if}>{tr}Space{/tr}</option>
							</select>
						</div>
						{*<input type="text" name="bookDecPoint" id="bookDecPoint" value="{$bookDecPoint}"></div>*}
						<div>
							<label class="aclabel">{tr}Thousands separator{/tr}</label>
							<select name="bookThousand" id="bookThousand" >
								<option value="," {if empty($bookThousand) or $bookThousand eq ','}selected="selected"{/if}>{tr}Comma{/tr}</option>
								<option value="." {if $bookThousand eq '.'}selected="selected"{/if}>{tr}Decimal point{/tr}</option>
								<option value=" " {if $bookThousand eq ' '}selected="selected"{/if}>{tr}Space{/tr}</option>
							</select>
						</div>
						{*<input type="text" name="bookThousand" id="bookThousand" value="{$bookThousand}"></div>*}
					</fieldset>
					<fieldset>
						<legend>CSV export settings</legend>
						<div>
							<label class="aclabel">{tr}Separator{/tr}</label>
							<input type="text" name="exportSeparator" id="exportSeparator" value="{$exportSeparator}">
						</div>
						<div>
							<label class="aclabel">{tr}Quote strings with{/tr}</label>
							<input type="text" name="exportQuote" id="exportQuote" value="{$exportQuote}">
						</div>
						<div>
							<label class="aclabel">{tr}End of Line{/tr}</label>
							<select name="exportEOL" id="exportEOL">
								<option value="CR"{if $exportEOL=='CR'} selected="selected"{/if}>{tr}Carriage return{/tr}</option>
								<option value="LF"{if $exportEOL=='LF'} selected="selected"{/if}>{tr}Line feed{/tr}</option>
								<option value="CRLF"{if $exportEOL=='CRLF'} selected="selected"{/if}>{tr}Carriage Return/Line feed{/tr}</option>
							</select>
						</div>
					</fieldset>
					<fieldset>
						<legend>{tr}Automatic Tax Calculation{/tr}</legend>
						<div>
							<div class="aclabel">
							<label class="aclabel">{tr}Allow automatic tax calculation{/tr}</label><br>&nbsp;</div>
							<div>
								<input type="radio" name="bookAutoTax" id="bookAutoTaxY"{if $bookAutoTax!='n'} checked="checked"{/if} value="y">{tr}Yes{/tr}<br>
								<input type="radio" name="bookAutoTax" id="bookAutoTaxN"{if $bookAutoTax=='n'} checked="checked"{/if} value="n">{tr}No{/tr}<br>
							</div>
						</div>
					</fieldset>
					<input type="submit" class="btn btn-default btn-sm" name="create" value="{tr}Create a new book{/tr}">
				</form>
			</div>
		{/tab}
	{/if}
{/tabset}
