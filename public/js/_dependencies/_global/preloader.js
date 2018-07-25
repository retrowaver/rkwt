class Preloader
{
	constructor()
	{
		this._loader = $("#preloader");
		this._depth = 0;
	}

	show()
	{
		this._depth++;
		this._loader.show();
	}

	hide(forced = false)
	{
		this._depth--;
		if (!this._depth || forced) {
			this._loader.hide();
		}
	}
}