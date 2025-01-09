<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\EnrolmentResponseModel;

class EnrolmentResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
	public function __construct(RequestInterface $request, $data)
	{
		parent::__construct($request, $data);

		$this->response = new EnrolmentResponseModel((array)$this->response);
	}

	public function getData(): EnrolmentResponseModel
	{
		return $this->response;
	}

	public function isSuccessful(): bool
	{
		return false;
	}

	public function getMessage(): string
	{
		return $this->response->errorMessage;
	}

	public function isRedirect(): bool
	{
		return $this->response->status === 'failure' ? false : true;
	} 

    public function getRedirectMethod()
    {
        return 'POST';
    }
	
	public function getRedirectResponse()
	{ 
		$response = parent::getRedirectResponse();

		$response->setContent(str_replace('<body', "<body style='color:#FFF'", $this->getData()->threeDSHtmlContent));

		$script = '<script>
			document.forms[0].style.display = "none";
	        document.getElementsByTagName("section")[0].style.display = "block";

			setTimeout(function() {
			  document.body.style.color = "auto";
			  document.forms[0].style.display = "block";
			  document.getElementsByTagName("section")[0].style.display = "none";
			}, 5000);
		</script>';

		$response->setContent(str_replace('</body>', "$script</body>", $response->getContent()));
 
		return $response;
	}

	public function getRedirectUrl()
	{
		return 'IYZICO-Sends-Premade-Form-So-No-Url-Needed';
	}
}
